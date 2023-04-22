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
                    <strong>Batch Routine</strong>
                  </v-list-item-title>
                  <v-list-item-subtitle
                    >Home <v-icon>mdi-forward</v-icon> Academics
                    <v-icon>mdi-forward</v-icon> 
                    Batch Routine</v-list-item-subtitle
                  >
                </v-list-item-content>
              </v-list-item>
            </v-toolbar-title>
            <v-spacer></v-spacer>
          </v-card-text>
          <v-row class="mx-4 pt-4">
              <v-col cols="12" md="3" class="mb-4 p-0">
                <v-select
                  v-model="selectedBatch"
                  :disabled="isexamTypeDataLoading"
                  :items="batchItems"
                  class="mt-4 mx-4"
                  dense
                  :label="lblSelectExamType"
                  item-text="lms_batch_code_name"
                  item-value="lms_batch_id"
                  @change="
                    isSearchByExamType = true;
                    getAllRoutine($event);
                  "
                ></v-select>
              </v-col>
        
            </v-row>
        </v-card>
      </v-row>
 
<v-card>
      <table>
      <thead>
        <tr>
          <th  rowspan="3">Week</th>
          <th v-for="column in tableWeek" :key="column.id">{{ column.title }}</th>
       
        </tr>
      </thead>
      <tbody>
     
        <tr v-for="(item, index) in tableItems" :key="index">
         
            <th >
              
              {{ index}}
      
            </th>

            <td  v-for="(item1, index1) in item" :key="index1" >
              <div v-if="item1 !=1">
                {{ item1.subject_name }}
                <br/>
                ( {{  item1.teacher_first_name.charAt(0).toUpperCase() }} 
                 {{  item1.teacher_last_name.charAt(0).toUpperCase()  }}
                )
                

              </div>
              <div v-else>
              No Class

              </div>
           
        
              </td>
  
          
          
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
      batchItems:[],
      selectedBatch:"",
      columns:[],
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
      lblSelectExamType: "Filter By Batch",
      selectedBatch: "",
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
     
        { id: 1, title: '5:00-6:00', field: '5:00 PM' },
        { id: 2, title: '6:00-7:00', field: '6:00 PM' },
        { id: 3, title: '7:00-8:00', field: '7:00 PM' },
        { id: 4, title: '8:00-9:00', field: '8:00 PM' },
      
      ],
      tableWeek: [
     
        { id: 1, title: 'Sunday', field: 'Sunday' },
        { id: 2, title: 'Monday', field: 'Monday' },
        { id: 3, title: 'Tuesday', field: 'Tuesday' },
        { id: 4, title: 'Wednesday', field: 'Wednesday' },
        { id: 5, title: 'Thursday', field: 'Thursday' },
        { id: 6, title: 'Friday', field: 'Friday' },
        { id: 7, title: 'Saturday', field: 'Saturday' },
      
      ],
      tableItems: [],
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
   this.getAllRoutine();
   this.getAllBatch();
  },

  methods: {

    getAllBatch()
    {
      this.$http
        .get(`web_get_all_batch_without_pagination`, {
       
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
            this.batchItems = data;
          //  this.class = data.data[0].lms_child_course_name;
        
            console.log(this.batchItems);
          }
        })
        .catch((error) => {
          this.tableDataLoading = false;
          this.snackBarColor = "error";
          this.changeSnackBarMessage(this.$t("label_something_went_wrong"));
        });
    
    },

    // Get all Staff from DB
    getAllRoutine() {
      let postData = "";

if (this.isSearchBySource == true) {
    postData = {
      batchId: this.selectedBatch,    
    };
} else {
    postData = {
        
        batchId: this.selectedBatch,
    };
}
      this.$http
        .get(`web_get_current_day_routine`, {
          params:postData,
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
        
            console.log(this.tableItems);
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
