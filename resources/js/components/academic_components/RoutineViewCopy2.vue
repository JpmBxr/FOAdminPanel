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
  <v-card>
        <table>
        <thead>
          <tr>
            <th>Class/Course</th>
            <th>5:00pm-6pm</th>
            <th>6:00pm-7pm</th>
            <th>7:00pm-8pm</th>
            <th>8:00pm-9pm</th>
          </tr>
        </thead>
        <tbody>
       
          <tr>
            <tr v-for="item in tableItems" :key="item.id">
              <td>{{ item.class }}
              
              ({{ item.slot1}})</td>
           
          
            
            </tr>
          </tr>
       
        </tbody>
      </table>
    </v-card>
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
       ""
        ],
        tableItems: [
        { id: 1, class: 'BZSVII', slot1: 'Mr. Smith', slot2: 'Ms. Brown', slot3: 'Mr. Green', slot4: 'Ms. White' },
                          { id: 2, class: 'Physics', slot1: 'Ms. Brown', slot2: 'Mr. Green', slot3: 'Ms. White', slot4: 'Mr. Smith' },
                          { id: 3, class: 'Chemistry', slot1: 'Mr. Green', slot2: 'Ms. White', slot3: 'Mr. Smith', slot4: 'Ms. Brown' },
        ],
        isDataProcessing: false,
        class:"",
  
      
      };
    },
    computed: {
      // For numbering the Data Table Rows
      dataTableRowNumbering() {
        return this.tableItems.map((items, index) => ({
          ...items,
        
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
   //  this.getAllRoutine();
    },
  
    methods: {
  
      // Get all Staff from DB
      getAllRoutine() {
  
        this.$http
          .get(`web_get_current_day_routine`, {
         
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
    
  
    },
  };
  </script>
  
  <style scoped>
   table {
          width: 100%;
          border-collapse: collapse;
        }
  
        th,
        td {
          padding: 10px;
          text-align: center;
          border: 1px solid #ccc;
        }
  
        th {
          background-color: #f2f2f2;
          font-size: 16px;
        }
  
        td {
          font-size: 14px;
        }
  
        @media screen and (max-width: 768px) {
          /* Responsive styles */
          table {
            font-size: 12px;
          }
  
          th,
          td {
            padding: 5px;
          }
        }
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
  