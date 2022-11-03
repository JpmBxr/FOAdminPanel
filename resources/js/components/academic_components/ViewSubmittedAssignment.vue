<template>
    <div style="background-color: #d7d8db; height:100%" id="app">
        <v-container
            style="background-color: #fff"
            class="ma-4 pa-0"
            width="100%"
        >
            <!-- Card Start -->
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
                                <strong> Evaluate Assignment</strong>
                            </v-list-item-title>
                            <v-list-item-subtitle
                                >Home <v-icon>mdi-forward</v-icon> Academics
                                <v-icon>mdi-forward</v-icon>
                                Evaluate Assignment</v-list-item-subtitle
                            >
                        </v-list-item-content>
                    </v-list-item>
                </v-toolbar-title>
                <v-spacer></v-spacer>
            </v-row>

            <transition name="fade" mode="out-in">
                <v-card>
                    <v-data-table
                        dense
                        :headers="tableHeader"
                        :items="dataTableRowNumbering"
                        item-key="lms_submitted_assignment_document_id"
                        class="elevation-1"
                        :loading="tableDataLoading"
                        :loading-text="tableLoadingDataText"
                        :server-items-length="totalItemsInDB"
                        :items-per-page="50"
                        @pagination="getAllAssignmentSubmittedByStudent"
                        :footer-props="{
                            itemsPerPageOptions: [25, 50, 100, 200, -1]
                        }"
                    >
                        <template v-slot:no-data>
                            <p
                                class="font-weight-black bold title"
                                style="color: red"
                            >
                                {{ $t("label_no_data_found") }}
                            </p>
                        </template>
                        <template
                            v-slot:item.lms_submitted_assignment_upload_status="{
                                item
                            }"
                        >
                            <v-chip
                                x-small
                                :color="
                                    getStatusColor(
                                        item.lms_submitted_assignment_evaluation_status
                                    )
                                "
                                dark
                                >{{
                                    item.lms_submitted_assignment_evaluation_status
                                }}</v-chip
                            >
                        </template>

                        <template v-slot:item.actions="{ item }">
                            <v-icon
                            
                                v-permission="'Edit Subject'"
                                @click="viewAssignment(item)"
                                >mdi-eye-outline</v-icon
                            >
                        </template>
                    </v-data-table>
                </v-card>
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
import DatetimePicker from "vuetify-datetime-picker";
//PDF Export
import jsPDF from "jspdf";
import "jspdf-autotable";
import Vue from "vue";
import { createLogger } from "vuex";
Vue.use(DatetimePicker);

import VueMask from "v-mask";
Vue.use(VueMask);

export default {
    props: ["userPermissionDataProps"],

    data() {
        return {
            isLoaderActive: false,

            authorizationConfig: "",
            tableItems: [],

            // Snack Bar

            isSnackBarVisible: false,
            snackBarMessage: "",
            snackBarColor: "",

            //   Datatable Start

            tableDataLoading: false,
            totalItemsInDB: 0,
            tableLoadingDataText: this.$t("label_loading_data"),

            tableHeader: [
                { text: "#", value: "index", width: "5%", sortable: false },
                {
                    text: "Title",
                    value: "lms_assignment_title",
                    width: "10%",
                    sortable: false
                },
                {
                    text: "Subject",
                    value: "lms_subject_name",
                    width: "25%",
                    sortable: false
                },
                {
                    text: "Topic",
                    value: "lms_topic_name",
                    width: "15%",
                    sortable: false
                },
                {
                    text: "Name",
                    value: "lms_student_full_name",
                    width: "10%",
                    sortable: false
                },
                {
                    text: "Code",
                    value: "lms_student_code",
                    width: "10%",
                    sortable: false
                },

                {
                    text: this.$t("label_status"),
                    value: "lms_submitted_assignment_upload_status",
                    align: "middle",
                    width: "10%",
                    sortable: false
                },
                {
                    text: this.$t("label_actions"),
                    value: "actions",
                    sortable: false,
                    width: "20%",
                    align: "end"
                }
            ]
        };
    },
    watch: {},
    computed: {
        // For numbering the Data Table Rows
        dataTableRowNumbering() {
            return this.tableItems.map((items, index) => ({
                ...items,
                index: index + 1
            }));
        }

        //End
    },

    created() {
        // Token Config
        this.authorizationConfig = {
            headers: { Authorization: "Bearer " + ls.get("token") }
        };
    },

    methods: {
        // Get all Subject from DB
        getAllAssignmentSubmittedByStudent(e) {
            this.tableDataLoading = true;

            this.$http

                .get(`web_get_get_all_submitted_assignment?page=${e.page}`, {
                    params: {
                        perPage:
                            e.itemsPerPage == -1
                                ? this.totalItemsInDB
                                : e.itemsPerPage
                    },
                    headers: { Authorization: "Bearer " + ls.get("token") }
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
                    }
                })
                .catch(error => {
                    this.tableDataLoading = false;
                    this.snackBarColor = "error";
                    this.changeSnackBarMessage(
                        this.$t("label_something_went_wrong")
                    );
                });
        },
        // Change Snack bar message
        changeSnackBarMessage(data) {
            this.isSnackBarVisible = true;
            this.snackBarMessage = data;
        },

        // Course Status Color
        getStatusColor(is_assignment_upload_status_active) {
            if (is_assignment_upload_status_active == "Evaluated")
                return "success";
            else return "error";
        },

        viewAssignment(item) {
            this.$router.push({
                name: "SubmittedAssignmentDetails",
                params: { assignmentDataProps: item }
            });
        }
    }
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

.fluid-background {
    background-color: blue;
}

.work-experiences > div {
    margin: 2px 0;
    padding-bottom: 2px;
}
.work-experiences > div:not(:last-child) {
    border-bottom: 0px solid rgb(206, 212, 218);
}
</style>
