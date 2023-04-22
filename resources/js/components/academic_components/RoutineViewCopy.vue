<template>
  <div id="app">
    <!-- Card Start -->
    <v-container
      fluid
      style="background-color: #e4e8e4; max-width: 100% !important"
    >
      <v-progress-linear
        :active="isDataProcessing"
        :indeterminate="isDataProcessing"
        height="10"
        absolute
        top
        color="primary"
        background-color="primary lighten-3"
        striped
      ></v-progress-linear>

      <v-row
        justify="space-around"
        style="margin-right: 1px !important; margin-left: -1px !important"
        class="mb-4 mt-1"
        dense
      >
        <v-card width="100%" elevation="0">
          <v-card-text>
            <v-toolbar-title dark color="primary">
              <v-list-item two-line>
                <v-list-item-content>
                  <v-list-item-title class="text-h5">
                    <strong>Routine</strong>
                  </v-list-item-title>
                  <v-list-item-subtitle
                    >Home <v-icon>mdi-forward</v-icon> Academics
                    <v-icon>mdi-forward</v-icon> 
                    Routine</v-list-item-subtitle
                  >
                </v-list-item-content>
              </v-list-item>
            </v-toolbar-title>
            <v-spacer></v-spacer>
          </v-card-text>
        </v-card>
      </v-row>

      <transition name="fade" mode="out-in">
        <v-data-table
          dense
          :headers="tableHeader"
          :items="dataTableRowNumbering"
          item-key="lms_exam_schedule_id"
          :loading="tableDataLoading"
          :loading-text="tableLoadingDataText"
          :server-items-length="totalItemsInDB"
          :items-per-page="200"
          @pagination="getAllExamSchedule"
          :footer-props="{
            itemsPerPageOptions: [200, 500, 1000, -1],
          }"
        >
          <template v-slot:no-data>
            <p class="font-weight-black bold title" style="color: red">
              {{ $t("label_no_data_found") }}
            </p>
          </template>

          <template v-slot:item.status="{ item }">
            <v-chip x-small :color="getStatusColor(item.status)" dark>{{
              item.status
            }}</v-chip>
          </template>

          <template v-slot:top>
            <v-toolbar flat>
              <v-text-field
                dense
                class="mt-4 mx-4"
                v-model="examSearchCriteria"
                :label="lblSearchExamCriteria"
                @input="searchTable($event)"
                prepend-inner-icon="mdi-magnify"
              ></v-text-field>

              <v-spacer></v-spacer>
        
            
            </v-toolbar>
       
          </template>
          >
     
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

    
      <!-- View Exam Schedule Doalig End -->
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
import { Global } from "../helpers/global";

export default {
  props: ["userPermissionDataProps"],

  data() {
    return {
      // For Breadcrumb
      breadCrumbItem: [
        {
          text: this.$t("label_home"),
          disabled: false,
        },
        {
          text: this.$t("label_exam_schedule"),
          disabled: false,
        },
        {
          text: this.$t("label_exam_schedule_directory"),
          disabled: false,
        },
      ],
      date : new Date(),
      selectedStartDate: moment().startOf('month').format('YYYY-MM-DD'),
     
      selectedEndDate:moment().endOf('month').format("YYYY-MM-DD"),
      viewExamScheduleDialog: false,
      // Form Data
      lblSelectExamType: "Filter By Status",
      selectedExamType: "",
      isDataProcessing: false,
      examSearchCriteria: "",
      lblSearchExamCriteria: "Search By Student Name | Course | Reg No",
      isexamTypeDataLoading: false,
      modalStartDate: false,
      modalEndDate: false,
    
      isSearchByExamType: false,
      authorizationConfig: "",
      dialog: false,

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
          text: "Class",
          value: this.class,
          width: "20%",
          sortable: false,
        },
        {
          text: "5:00-6:00",
          value: "lms_subject_name",
          width: "20%",
          sortable: false,
        },
        {
          text: "6:00-7:00",
          value: "lms_receipt_no",
          width: "10%",
          sortable: false,
        },

      
        {
          text: "7:00-8:00",
          value: "",
          width: "10%",
          sortable: false,
        },
        {
          text: "8:00-9:00",
          value: "month",
          width: "10%",
          sortable: false,
        },

    
      ],
      tableItems: [],
      isDataProcessing: false,
      class:"",

      //Datatable End

      //View props
      lms_exam_schedule_id: "",
      lms_exam_schedule_type: "",
      lms_exam_schedule_name: "",
      lms_exam_schedule_description: "",
      lms_exam_schedule_negative_marking: "",
      lms_exam_schedule_result_display_type: "",
      lms_exam_schedule_no_of_question: "",
      lms_exam_schedule_max_marks: "",
      lms_exam_schedule_total_time_alloted: "",
      lms_exam_schedule_is_free_paid_status: "",
      lms_exam_schedule_pass_marks: "",
      lms_exam_schedule_has_negative_marking_status: "",
      lms_exam_schedule_image: "",
      lms_exam_schedule_image_alt: "",
      lms_exam_instruction_title: "",
      lms_course_name: "",
      lms_topic_name: "",
      lms_subject_name: "",
      lms_exam_instruction: "",
      lms_exam_schedule_is_active_status: "",
      lms_exam_schedule_live_from: "",
      lms_exam_schedule_live_to: "",
      lms_exam_schedule_is_active_live: "",


    };
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

    // Get all active sources
  
  },

  methods: {
    sumField(key) {
      // sum data in give key (property)
      return this.tableItems.reduce(
        (a, b) => parseInt(a) + (parseInt(b[key]) || 0),
        0
      );
    },
    searchTable(e) {
      clearTimeout(this._timerId);
      this._timerId = setTimeout(() => {
        this.isSearchByExamType = false;
        this.getAllExamSchedule(e);
      }, Global.SearchTimeOut);
    },
  
  

    changeSnackBarMessage(data) {
      this.isSnackBarVisible = true;
      this.snackBarMessage = data;
    },
    // View Staff
    viewExamSchedule(item) {
      this.lms_exam_schedule_id = item.lms_exam_schedule_id;
      this.lms_exam_schedule_type = item.lms_exam_schedule_type;
      this.lms_exam_schedule_name = item.lms_exam_schedule_name;
      this.lms_exam_schedule_description = item.lms_exam_schedule_description;
      this.lms_exam_schedule_negative_marking =
        item.lms_exam_schedule_negative_marking;
      this.lms_exam_schedule_result_display_type =
        item.lms_exam_schedule_result_display_type;
      this.lms_exam_schedule_no_of_question =
        item.lms_exam_schedule_no_of_question;
      this.lms_exam_schedule_max_marks = item.lms_exam_schedule_max_marks;
      this.lms_exam_schedule_total_time_alloted =
        item.lms_exam_schedule_total_time_alloted;
      this.lms_exam_schedule_is_free_paid_status =
        item.lms_exam_schedule_is_free_paid_status;
      this.lms_exam_schedule_pass_marks = item.lms_exam_schedule_pass_marks;
      this.lms_exam_schedule_has_negative_marking_status =
        item.lms_exam_schedule_has_negative_marking_status;
      this.lms_exam_schedule_image =
        process.env.MIX_EXAM_PROFILE_IMAGE_URL + item.lms_exam_schedule_image;
      this.lms_exam_schedule_image_alt =
        process.env.MIX_EXAM_PROFILE_IMAGE_URL + "Exams.jpg";
      this.lms_course_name = item.lms_course_name;
      this.lms_exam_instruction_title = item.lms_exam_instruction_title;
      this.lms_exam_instruction = item.lms_exam_instruction;
      this.lms_exam_schedule_is_active_status =
        item.lms_exam_schedule_is_active_status;
      this.lms_exam_schedule_live_from = item.lms_exam_schedule_live_from;
      this.lms_exam_schedule_live_to = item.lms_exam_schedule_live_to;
      this.lms_exam_schedule_is_active_live =
        item.lms_exam_schedule_is_active_live;

      this.viewExamScheduleDialog = true;
    },

    // Edit Exam Schedule
    ViewStudentDetails(item) {
      this.$router.push({
        name: "ViewStudentDetailsExamReport",
        params: { examReportDataProps: item },
      });
    },

    // Get all Staff from DB
    getAllExamSchedule(e) {
      this.tableDataLoading = true;
      let postData = "";
      if (this.isSearchByExamType == true) {
        postData = {
          centerId: ls.get("loggedUserCenterId"),
          perPage: e.itemsPerPage == -1 ? this.totalItemsInDB : e.itemsPerPage,
          isPaid: this.selectedExamType,
          startDate: this.selectedStartDate,
          endDate: this.selectedEndDate,
        };
      } else {
        postData = {
          centerId: ls.get("loggedUserCenterId"),
          perPage: e.itemsPerPage == -1 ? this.totalItemsInDB : e.itemsPerPage,
          filterBy: this.examSearchCriteria,
          startDate: this.selectedStartDate,
          endDate: this.selectedEndDate,
        };
      }

      this.$http
        .get(`web_get_current_day_routine?page=${e.page}`, {
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
          //  this.class = data.data[0].lms_child_course_name;
            this.totalItemsInDB = data.total;
            this.excelData = data.data;
            console.log(this.class);
          }
        })
        .catch((error) => {
          this.tableDataLoading = false;
          this.snackBarColor = "error";
          this.changeSnackBarMessage(this.$t("label_something_went_wrong"));
        });
    },
  
    // Export to PDF
    savePDF() {
      const pdfDoc = new jsPDF({ orientation: "landscape" });

      pdfDoc.autoTable({
        columns: [
          { header: "StudentName", dataKey: "lms_student_full_name" },
          { header: "RegistrationId", dataKey: "lms_student_reg_id" },

          {
            header: "Mobile_number",
            dataKey: "lms_student_mobile_number",
          },
          {
            header: "Manual_Receipt_No",
            dataKey: "lms_manual_receipt_no",
          },
          {
            header: "Course/Class",
            dataKey: "lms_child_course_name",
          },
          { header: "Month", dataKey: "month" },
          { header: "Fee", dataKey: "lms_actual_fee" },
          { header: "DueDate", dataKey: "due_date" },
          { header: "PaidDate", dataKey: "paid_date" },
          { header: "Status", dataKey: "status" },
          { header: "TotalAmount", dataKey: "total_amount" },
          { header: "DueAmount", dataKey: "due_amount" },
          { header: "PaidAmount", dataKey: "lms_cash_receipt" },
        ],
        body: this.tableItems,
        //styles: { fillColor: [255, 0, 0] },
        // columnStyles: { 0: { halign: 'center', fillColor: [0, 255, 0] } },
        margin: { top: 10 },
      });
      pdfDoc.save("FeeReport" + "_" + moment().format("DD/MM/YYYY") + ".pdf");
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

.v-sheet--offset {
  top: -24px;
  position: relative;
}
.v-data-table > .v-data-table__wrapper > table > tbody > tr > td,
.v-data-table > .v-data-table__wrapper > table > tbody > tr > th,
.v-data-table > .v-data-table__wrapper > table > tfoot > tr > td,
.v-data-table > .v-data-table__wrapper > table > tfoot > tr > th,
.v-data-table > .v-data-table__wrapper > table > thead > tr > td,
.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  padding: 0 2px !important;
}
</style>
