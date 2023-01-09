<template>
  <div style=" margin:auto; padding:auto; width:1200px;" id="app">
    <v-container style="background-color: #fff" class="ma-4 pa-0" width="100%">
      <v-overlay :value="isLoaderActive" color="primary">
        <v-progress-circular
          indeterminate
          size="64"
          color="primary"
        ></v-progress-circular>
      </v-overlay>
      <v-row class="ml-4 mr-4 pt-4">
        <v-toolbar-title dark color="primary">
          <v-list-item two-line>
            <v-list-item-content>
              <v-list-item-title class="text-h5">
                <strong> {{ this.$t("label_notice") }}</strong>
              </v-list-item-title>
              <v-list-item-subtitle
                >Home <v-icon>mdi-forward</v-icon> Communication
                <v-icon>mdi-forward</v-icon>
                {{ this.$t("label_notice") }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
          v-permission="'Add Notice'"
          v-if="!isAddCardVisible"
          :disabled="tableDataLoading"
          color="primary"
          class="white--text"
          @click="isAddCardVisible = !isAddCardVisible"
        >
          Add Notice
          <v-icon right dark> mdi-plus </v-icon>
        </v-btn>
      </v-row>
      <transition name="fade" mode="out-in">
        <v-card v-if="isAddCardVisible">
          <v-app-bar dark color="grey" flat>
            <v-toolbar-title color="success">{{
              $t("label_notice")
            }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon dark>
              <v-icon @click="isAddCardVisible = !isAddCardVisible"
                >mdi-close</v-icon
              >
            </v-btn>
          </v-app-bar>

          <v-form
            ref="saveNoticeForm"
            v-model="isSaveNoticeFormValid"
            lazy-validation
          >
            <v-row dense class="mx-2">
              <v-col cols="12" md="6" sm="12">
                <v-select
                  outlined
                  dense
                  v-model="lms_notice_type"
                  :items="noticeTypeItems"
                  :disabled="isSourceDataLoading"
                  item-text="lms_notice_type_data"
                  item-value="lms_notice_type_value"
                  :rules="[(v) => !!v || $t('label_required')]"
                >
                  <template #label>
                    {{ $t("label_notice_type") }}
                    <span class="red--text">
                      <strong>{{ $t("label_star") }}</strong>
                    </span>
                  </template>
                </v-select>
              </v-col>

              <v-col cols="12" md="6" sm="12">
                <v-select
                  outlined
                  multiple
                  dense
                  v-model="roleId"
                  :items="roleItems"
                  :disabled="isSourceDataLoading"
                  item-text="name"
                  item-value="id"
                  :rules="[(v) => !!v || $t('label_required')]"
                >
                  <template #label>
                    {{ $t("label_notice_to") }}
                    <span class="red--text">
                      <strong>{{ $t("label_star") }}</strong>
                    </span>
                  </template>
                </v-select>
              </v-col>

              <v-col cols="12" xs="12" sm="12" md="12">
                <v-text-field
                  outlined
                  dense
                  v-model="lms_notice_title"
                  :rules="[(v) => !!v || $t('label_required')]"
                >
                  <template #label>
                    {{ $t("label_notice_title") }}
                    <span class="red--text">
                      <strong>{{ $t("label_star") }}</strong>
                    </span>
                  </template>
                </v-text-field>
              </v-col>
            </v-row>

            <v-row dense class="mx-2">
              <v-col cols="12" xs="12" sm="12" md="12">
                <div id="app">
                  <vue-editor
                    v-model="lms_notice_description"
                    :editor-toolbar="customToolbar"
                    :rules="[(v) => !!v || $t('label_required')]"
                  >
                    ></vue-editor
                  >
                </div>
              </v-col>
            </v-row>

            <v-card-actions>
              <v-btn
                class="ma-2 rounded"
                tile
                color="primary"
                :disabled="
                  !isSaveNoticeFormValid || isSaveNoticeFormDataProcessing
                "
                @click="saveNotice"
                >{{
                  isSaveNoticeFormDataProcessing == true
                    ? $t("label_processing")
                    : $t("label_save")
                }}</v-btn
              >
              <v-btn
                class="ma-2 rounded"
                tile
                color="error"
                :disabled="
                  !isSaveNoticeFormValid || isSaveNoticeFormDataProcessing
                "
                @click="
                  isAddCardVisible = !isAddCardVisible;
                  resetForm();
                "
                >{{
                  isSaveNoticeFormDataProcessing == true
                    ? $t("label_processing")
                    : $t("label_cancel")
                }}</v-btn
              >
            </v-card-actions>
          </v-form>
        </v-card>
      </transition>
      <transition name="fade" mode="out-in">
        <v-data-table
          dense
          :headers="tableHeader"
          :items="dataTableRowNumbering"
          item-key="lms_notice_id"
          class="elevation-1"
          :loading="tableDataLoading"
          :loading-text="tableLoadingDataText"
          :server-items-length="totalItemsInDB"
          :items-per-page="50"
          :single-expand="singleExpand"
          :expanded.sync="expanded"
          show-expand
          @pagination="getAllNotice"
          :footer-props="{
            itemsPerPageOptions: [50, 100, 150, 200, -1],
          }"
        >
          <template v-slot:expanded-item="{ headers, item }">
            <td :colspan="headers.length">
              <br />
              <p>
                <strong>Notice Description</strong>
              </p>
              <br />
              <div
                v-html="item.lms_notice_description"
                style="align-content: center"
              ></div>
              <br /><br />
            </td>
          </template>
          <template v-slot:no-data>
            <p class="font-weight-black bold title" style="color: red">
              {{ $t("label_no_data_found") }}
            </p>
          </template>
          <template v-slot:item.lms_notice_is_active="{ item }">
            <v-chip
              x-small
              :color="getStatusColor(item.lms_notice_is_active)"
              dark
              >{{ item.lms_notice_is_active }}</v-chip
            >
          </template>
          <template v-slot:top>
            <v-toolbar flat>
              <v-text-field
                prepend-inner-icon="mdi-magnify"
                class="mt-4 mx-4"
                :disabled="isSourceDataLoading"
                v-model="txtSearchCriteria"
                label="Search Here"
                @input="
                  isSearchBySource = false;
                  getAllNotice($event);
                "
              ></v-text-field>
              <v-spacer></v-spacer>
              <v-switch
                v-model="singleExpand"
                class="mt-6"
                v-if="!tableDataLoading"
              ></v-switch>

              <v-divider
                class="ml-6 mr-6"
                v-if="!tableDataLoading"
                inset
                vertical
              ></v-divider>
              <v-btn icon small color="teal" v-if="!tableDataLoading">
                <download-excel
                  :data="excelData"
                  :fields="excelFields"
                  :name="excelFileName"
                >
                  <v-icon dark>mdi-file-excel</v-icon>
                </download-excel>
              </v-btn>

              <v-btn
                v-if="!tableDataLoading"
                class="mr-2"
                icon
                small
                color="red"
                @click="savePDF"
              >
                <v-icon dark>mdi-file-pdf</v-icon>
              </v-btn>
            </v-toolbar>
          </template>

          <template v-slot:item.actions="{ item }">
            <v-icon
              v-permission="'Edit Notice'"
              small
              class="mr-2"
              @click="editNotice(item)"
              color="primary"
              >mdi-pencil</v-icon
            >

            <v-icon
              v-if="item.lms_notice_is_active == 'Active'"
              v-permission="'Delete Notice'"
              small
              color="error"
              @click="disableNotice(item)"
              >mdi-delete</v-icon
            >
            <v-icon
              v-if="item.lms_notice_is_active == 'Inactive'"
              v-permission="'Delete Notice'"
              small
              color="success"
              @click="disableNotice(item)"
              >mdi-check-circle</v-icon
            >
          </template>
        </v-data-table>
      </transition>

      <v-snackbar
        v-model="isSnackBarVisible"
        :color="snackBarColor"
        multi-line="multi-line"
        right="right"
        :timeout="3000"
        top="top"
        vertical="vertical"
        >{{ snackBarMessage }}</v-snackbar
      >
    </v-container>
  </div>
</template>
<script>
// Secure Local Storage
import SecureLS from "secure-ls";
const ls = new SecureLS({ encodingType: "aes" });

//PDF Export
import jsPDF from "jspdf";
import "jspdf-autotable";

import { VueEditor } from "vue2-editor";
import katex from "katex";
import "katex/dist/katex.min.css";

export default {
  components: {
    VueEditor,
  },
  props: ["userPermissionDataProps"],

  data() {
    return {
      customToolbar: [
        [{ header: [1, 2, 3, 4, 5, 6, false] }],
        ["bold", "italic", "underline", "strike", { align: [] }],

        ["blockquote", "code-block"],

        [{ list: "ordered" }, { list: "bullet" }],
        [{ script: "sub" }, { script: "super" }],
        [{ indent: "-1" }, { indent: "+1" }],
        [{ direction: "rtl" }],

        [{ color: [] }, { background: [] }],
      ],
      expanded: [],
      singleExpand: true,
      isLoaderActive: false,
      noticeId: "",
      isSearchBySource: false,
      txtSearchCriteria: null,
      lms_notice_description: "",
      lms_notice_title: "",
      isSaveNoticeFormValid: true,
      isSaveNoticeFormDataProcessing: false,
      authorizationConfig: "",
      roleItems: [],
      roleId: [],

      isAddCardVisible: false,

      // Snack Bar

      isSnackBarVisible: false,
      snackBarMessage: "",
      snackBarColor: "",

      //   Datatable Start

      tableDataLoading: false,
      totalItemsInDB: 0,
      tableLoadingDataText: this.$t("label_loading_data"),

      tableHeader: [
        {
          text: "#",
          value: "index",
          width: "5%",
          sortable: false,
          groupable: true,
        },
        {
          text: this.$t("label_notice_title"),
          value: "lms_notice_title",
          width: "20%",
          sortable: false,
        },
        {
          text: this.$t("label_notice_type"),
          value: "lms_notice_type",
          width: "20%",
          sortable: false,
        },
        {
          text: this.$t("label_notice_receiver"),
          value: "role_name",
          width: "10%",
          sortable: false,
        },

        {
          text: this.$t("label_notice_addedon"),
          value: "lms_notice_created_at",
          width: "15%",
          sortable: false,
        },

        {
          text: this.$t("label_status"),
          value: "lms_notice_is_active",
          align: "middle",
          width: "10%",
          sortable: false,
        },
        {
          text: this.$t("label_actions"),
          value: "actions",
          sortable: false,
          width: "10%",
          align: "middle",
        },
        {
          text: "Description",
          value: "data-table-expand",
        },
      ],
      tableItems: [],
      isSaveEditClicked: 1,
      editNoticeId: "",
      noticeImageNameForEdit: "",
      lms_notice_type: "",
      //Datatable End
      noticeTypeItems: [
        {
          lms_notice_type_data: "General Notice",
          lms_notice_type_value: "General Notice",
        },
        {
          lms_notice_type_data: "Batch Notice",
          lms_notice_type_value: "Batch Notice",
        },
        {
          lms_notice_type_data: "Holiday Notice",
          lms_notice_type_value: "Holiday Notice",
        },
        {
          lms_notice_type_data: "Class Notice",
          lms_notice_type_value: "Class Notice",
        },
        {
          lms_notice_type_data: "Urgent Notice",
          lms_notice_type_value: "Urgent Notice",
        },
      ],
      // For Excel Download
      excelFields: {
        Title: "lms_notice_title",
        Description: "lms_notice_description",
        Date: "lms_notice_created_at",
        Role: "role_name",
        Status: "lms_notice_is_active",
      },
      excelData: [],
      excelFileName: "Notice" + "_" + moment().format("DD/MM/YYYY") + ".xls",
    };
  },
  watch: {
    selectedCourseImage(val) {
      this.selectedCourseImage = val;

      this.imageRule =
        this.selectedCourseImage != null
          ? [
              (v) =>
                !v ||
                v.size <= 1048576 ||
                this.$t("label_file_size_criteria_1_mb"),
            ]
          : [];
    },
  },
  computed: {
    // For numbering the Data Table Rows
    dataTableRowNumbering() {
      return this.tableItems.map((items, index) => ({
        ...items,
        index: index + 1,
      }));
    },
    //End
  },

  created() {
    // Token Config
    this.authorizationConfig = {
      headers: { Authorization: "Bearer " + ls.get("token") },
    };
    // Get Active Sources
    this.getAllRole();
  },

  methods: {
    resetForm() {
      this.$refs.saveNoticeForm.reset();
      this.lms_notice_description = null;
    },
    // Get all active sources
    getAllActiveCourses() {
      this.isSourceDataLoading = true;
      this.$http
        .get(`web_get_all_active_courses_without_pagination`, {
          params: {
            centerId: ls.get("loggedUserCenterId"),
          },
          headers: { Authorization: "Bearer " + ls.get("token") },
        })
        .then(({ data }) => {
          this.isSourceDataLoading = false;
          //User Unauthorized
          if (
            data.error == "Unauthorized" ||
            data.permissionError == "Unauthorized"
          ) {
            this.$store.dispatch("actionUnauthorizedLogout");
          } else {
            //this.noticeTypeItems = data;
          }
        })
        .catch((error) => {
          this.isSourceDataLoading = false;
          this.snackBarColor = "error";
          this.changeSnackBarMessage(this.$t("label_something_went_wrong"));
        });
    },

    // Get all active subject based on course
    getAllRole() {
      this.isSourceDataLoading = true;
      this.$http
        .get(`web_get_all_role_without_pagination`, {
          params: {
            centerId: ls.get("loggedUserCenterId"),
          },
          headers: { Authorization: "Bearer " + ls.get("token") },
        })
        .then(({ data }) => {
          this.isSourceDataLoading = false;
          //User Unauthorized
          if (
            data.error == "Unauthorized" ||
            data.permissionError == "Unauthorized"
          ) {
            this.$store.dispatch("actionUnauthorizedLogout");
          } else {
            this.roleItems = data;
          }
        })
        .catch((error) => {
          this.isSourceDataLoading = false;
          this.snackBarColor = "error";
          this.changeSnackBarMessage(this.$t("label_something_went_wrong"));
        });
    },

    // Change Snack bar message
    changeSnackBarMessage(data) {
      this.isSnackBarVisible = true;
      this.snackBarMessage = data;
    },

    // Edit Course

    editNotice(item) {
      this.isAddCardVisible = true;
      this.editNoticeId = item.lms_notice_id;
      this.isSaveEditClicked = 0;
      this.noticeId = item.lms_notice_id;
      this.lms_notice_title = item.lms_notice_title;
      this.lms_notice_description = item.lms_notice_description;
      this.roleId = JSON.parse("[" + item.role_id + "]");
      this.lms_notice_type = item.lms_notice_type;
    },
    // Disable Course status
    disableNotice(item, $event) {
      let userDecision = confirm(
        this.$t("label_are_you_sure_change_status_notice")
      );
      if (userDecision) {
        this.isLoaderActive = true;
        this.$http
          .post(
            "web_enable_disable_notice",
            {
              centerId: ls.get("loggedUserCenterId"),
              lms_notice_id: item.lms_notice_id,
              isNoticeActive: item.lms_notice_is_active == "Active" ? 0 : 1,
              loggedUserId: ls.get("loggedUserId"),
            },
            this.authorizationConfig
          )
          .then(({ data }) => {
            this.isLoaderActive = false;
            //User Unauthorized
            if (
              data.error == "Unauthorized" ||
              data.permissionError == "Unauthorized"
            ) {
              this.$store.dispatch("actionUnauthorizedLogout");
            } else {
              // Course Disabled
              if (data.responseData == 1) {
                this.snackBarColor = "success";
                this.changeSnackBarMessage(
                  this.$t("label_topic_status_changed")
                );
                this.getAllNotice(event);
              }
              // Course Disabled failed
              else if (data.responseData == 2) {
                this.snackBarColor = "error";
                this.changeSnackBarMessage(
                  this.$t("label_something_went_wrong")
                );
              }
            }
          })
          .catch((error) => {
            this.isLoaderActive = false;
            this.snackBarColor = "error";
            this.changeSnackBarMessage(this.$t("label_something_went_wrong"));
          });
      }
    },
    // To ensure course name is character and space only
    isCharacters(evt) {
      var regex = new RegExp("^[a-zA-Z ]+$");
      var key = String.fromCharCode(!evt.charCode ? evt.which : evt.charCode);
      if (!regex.test(key)) {
        evt.preventDefault();

        return false;
      }
    },
    // Save Subject To DB after validation
    saveNotice($event) {
      if (this.$refs.saveNoticeForm.validate()) {
        this.isSaveNoticeFormDataProcessing = true;
        let postData = new FormData();
        if (this.isSaveEditClicked == 1) {
          postData.append("lms_notice_title", this.lms_notice_title);
          postData.append(
            "lms_notice_description",
            this.lms_notice_description
          );
          postData.append("role_id", this.roleId);
          postData.append("lms_notice_type", this.lms_notice_type);
          postData.append("isSaveEditClicked", 1);
          postData.append("centerId", ls.get("loggedUserCenterId"));
          postData.append("loggedUserId", ls.get("loggedUserId"));

          postData.append(
            "lms_staff_first_name",
            ls.get("lms_staff_first_name")
          );
          postData.append("lms_staff_last_name", ls.get("lms_staff_last_name"));
          postData.append("lms_staff_full_name", ls.get("lms_staff_full_name"));
          postData.append(
            "lms_staff_profile_image",
            ls.get("lms_staff_profile_image")
          );
          postData.append("role_name", ls.get("role_name"));
        } else {
          postData.append("lms_notice_title", this.lms_notice_title);
          postData.append(
            "lms_notice_description",
            this.lms_notice_description
          );
          postData.append("role_id", this.roleId);
          postData.append("lms_notice_type", this.lms_notice_type);
          postData.append("isSaveEditClicked", 0);
          postData.append("centerId", ls.get("loggedUserCenterId"));
          postData.append("loggedUserId", ls.get("loggedUserId"));
          postData.append("lms_notice_id", this.editNoticeId);

          postData.append(
            "lms_staff_first_name",
            ls.get("lms_staff_first_name")
          );
          postData.append("lms_staff_last_name", ls.get("lms_staff_last_name"));
          postData.append("lms_staff_full_name", ls.get("lms_staff_full_name"));
          postData.append(
            "lms_staff_profile_image",
            ls.get("lms_staff_profile_image")
          );
          postData.append("role_name", ls.get("role_name"));
        }
        this.$http
          .post("web_save_update_notice", postData, this.authorizationConfig)
          .then(({ data }) => {
            this.isSaveNoticeFormDataProcessing = false;
            //User Unauthorized
            if (
              data.error == "Unauthorized" ||
              data.permissionError == "Unauthorized"
            ) {
              this.$store.dispatch("actionUnauthorizedLogout");
            } else {
              // Server side validation fails
              if (data.responseData == 0) {
                this.snackBarColor = "error";
                this.changeSnackBarMessage(data.error);
              }
              // Course Code Exists
              else if (data.responseData == 1) {
                this.snackBarColor = "info";
                this.changeSnackBarMessage(this.$t("label_topic_exists"));
              }
              // Course Saved
              else if (data.responseData == 2) {
                this.lms_notice_type = null;
                this.lms_notice_title = "";
                this.lms_notice_description = null;
                this.isSaveEditClicked = 1;
                this.roleId = [];
                this.snackBarColor = "success";
                this.changeSnackBarMessage(this.$t("label_notice_saved"));
                this.getAllNotice(event);
                this.resetForm();
              }
              // Failed to save Course
              else if (data.responseData == 3) {
                this.snackBarColor = "error";
                this.changeSnackBarMessage(
                  this.$t("label_something_went_wrong")
                );
              }

              // Subject Updated
              else if (data.responseData == 4) {
                this.snackBarColor = "success";
                this.changeSnackBarMessage(this.$t("label_notice_updated"));
                this.resetForm();
                this.getAllNotice(event);
                this.isSaveEditClicked = 0;
                this.lms_notice_type = "";
                this.lms_notice_title = "";
                this.lms_notice_description = "";
                this.roleId = [];
              }
              // Course update failed
              else if (data.responseData == 5) {
                this.snackBarColor = "error";
                this.changeSnackBarMessage(
                  this.$t("label_something_went_wrong")
                );
              }
              // Image upload failed
              else if (data.responseData == 6) {
                this.snackBarColor = "error";
                this.changeSnackBarMessage(
                  this.$t("label_image_upload_failed")
                );
              }
            }
          })
          .catch((error) => {
            this.isSaveNoticeFormDataProcessing = false;
            this.snackBarColor = "error";
            this.changeSnackBarMessage(this.$t("label_something_went_wrong"));
          });
      }
    },

    // Get all Topic from DB
    getAllNotice(e) {
      this.tableDataLoading = true;
      let postData = "";
      if (this.isSearchBySource == true) {
        postData = {
          centerId: ls.get("loggedUserCenterId"),
          perPage: e.itemsPerPage == -1 ? this.totalItemsInDB : e.itemsPerPage,
          filterBy: this.txtSearchCriteria,
        };
      } else {
        postData = {
          centerId: ls.get("loggedUserCenterId"),
          perPage: e.itemsPerPage == -1 ? this.totalItemsInDB : e.itemsPerPage,
          filterBy: this.txtSearchCriteria,
        };
      }
      this.$http
        .get(`web_get_all_notice?page=${e.page}`, {
          params: postData,
          headers: { Authorization: "Bearer " + ls.get("token") },
        })
        .then(({ data }) => {
          this.tableDataLoading = false;
          //User Unauthorized
          if (
            data.error == "Unauthorized" ||
            data.permissionError == "Unauthorized"
          ) {
            this.$store.dispatch("actionUnauthorizedLogout");
          } else {
            this.tableItems = data.data;
            this.totalItemsInDB = data.total;
            this.excelData = data.data;
          }
        })
        .catch((error) => {
          this.tableDataLoading = false;
          this.snackBarColor = "error";
          this.changeSnackBarMessage(this.$t("label_something_went_wrong"));
        });
    },
    // Topic Status Color
    getStatusColor(is_course_active) {
      if (is_course_active == "Active") return "success";
      else return "error";
    },
    // Export to PDF
    savePDF() {
      const pdfDoc = new jsPDF();
      pdfDoc.autoTable({
        columns: [
          { header: "Topic Code", dataKey: "lms_topic_code" },
          { header: "Topic Name", dataKey: "lms_topic_name" },
          { header: "Course Name", dataKey: "lms_course_name" },
          { header: "Subject Name", dataKey: "lms_subject_name" },
          { header: "Status", dataKey: "lms_notice_is_active" },
        ],
        body: this.tableItems,
        //styles: { fillColor: [255, 0, 0] },
        // columnStyles: { 0: { halign: 'center', fillColor: [0, 255, 0] } },
        margin: { top: 10 },
      });
      pdfDoc.save(
        "TopicListAsPDF" + "_" + moment().format("DD/MM/YYYY") + ".pdf"
      );
    },
  },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition-duration: 0.9s;
  transition-property: opacity;
  transition-timing-function: ease;
}

.fade-enter,
.fade-leave-active {
  opacity: 0;
}
</style>
