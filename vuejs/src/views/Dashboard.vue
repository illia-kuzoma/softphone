<template>
    <div
      id="dashboard"
      >
      <HeaderComponent/>

      <div
        class="dashboard"
        >

        <div class="header-container">
          <h1>Missed Calls</h1>
        </div>

        <div class="controls-container">
          <div class="date-container">
            <button
              class="button button-li date-item color-grey color-dark"
               v-on:click="setDate('today'); setActive($event)"
              >Today
            </button>

            <date-picker
              v-model="selectedDate"
              type="date"
              class="date-item"
              valueType="YYYY-MM-DD"
              format="DD/MM/YYYY"
            ></date-picker>

            <button
              class="button button-li date-item color-grey"
              v-on:click="setDate('day'); setActive($event)"
              >Day
            </button>
            <button
              class="button button-li date-item color-grey"
              v-on:click="setDate('week'); setActive($event)"
              >Week
            </button>
            <button
              class="button button-li date-item color-grey"
              v-on:click="setDate('month'); setActive($event)"
              >Month
            </button>
            <button
              class="button button-li date-item color-grey"
              v-on:click="setDate('year'); setActive($event)"
              >Year
            </button>
          </div>
          <div class="search-container">
            <input
              v-model="searchText"
              type="text"
              id="search"
              placeholder="Search..."
              name="search">
              <button
                id="search-button"
                class="button color-violet"
                v-on:click="search(searchText) ; "
                >Search
              </button>
          </div>
        </div>

        <div class="chart-container" v-if="Object.keys(chartData).length !== 0">
          <h2>MISSED CALLS</h2>
          <line-chart
            id="chartId"
            v-if='chartData'
            :chartObject='chartData'
            :is-resizable="true"
            :use-css-transforms="true"
            @clicked="getAgentFromChart"
            class="chart">
          </line-chart>
        </div>

        <div class="table-container" v-if='Object.keys(chartData).length === 0'>
          <h1>No Chart Data For This Period</h1>
        </div>

        <div class="table-container" v-if='tableCallsData.length>0'>
          <v-data-table
            :headers="tableCallsHeaders"
            :items="tableCallsData"
            :items-per-page="20"
            :page.sync="tablePage"
            :options.sync='options'
            :hide-default-footer="true"
            class="v-data-table elevation-1"
            fixed-header
            @update:options="getDataFromTableRow"
            >
              <template v-slot:item.user_data="{ item }">
                <div class='user'>
                  <img :src="item.user_data.photo_url" alt="">
                  <p>{{ item.user_data.full_name }}</p>
                </div>
              </template>
              
              <template v-slot:item.business="{ item }">
                <div class='business'>
                  <a
                    target="_blank"
                    v-on:click='goOutTo(item.business.business_link)'
                    class='url'
                    :href="item.business.business_link"
                    >{{ item.business.business_name }}
                  </a>
                </div>
              </template>

              <template v-slot:item.contact="{ item }">
                <div class='user'>
                  <a
                    target="_blank"
                    v-on:click='goOutTo(item.contact.contact_link)'
                    class='url'
                    :href="item.contact.contact_link"
                    >{{ item.contact.contact_name }}
                  </a>
                </div>
              </template>

              <template v-slot:item.time_create="{ item }">
                <div class='table-date-cell' >{{getDate(item["time_create"])}}</div>
              </template>

          </v-data-table>
          <v-pagination
            v-if="tablePageCount>1"
            v-model="tablePage"
            :length="tablePageCount"
            class="table-pagination"
            @input="changePage"
          ></v-pagination>
        </div>

        <div class="table-container" v-if='tableCallsData.length==0'>
          <h1>No Table Data For This Period</h1>
        </div>

      </div>
    </div>
</template>

<script>
  import HeaderComponent from '@/components/header.vue'
  import DatePicker from 'vue2-datepicker';
  import 'vue2-datepicker/index.css'
  import LineChart from '@/components/Bar.vue'
  import HttpService from '@/services/HttpService'
  import moment from 'moment'

  export default {
    name: 'HelloWorld',
    components: { 
      HeaderComponent,
      DatePicker,
      LineChart,
    },
    data: () => ({
      firstLoad:true,
      selectedDate:null,
      dateType:'date',
      chartData:[],
      tableCallsData:[],
      serverChartData:null,
      options: {},
      tableCallsHeaders: [
        { text: 'Agent', value: 'user_data' },
        { text: 'Business Name', value: 'business' },
        { text: 'Contact', value: 'contact' ,width: 200},
        { text: 'Priority', value: 'priority', sortable: false},
        { text: 'Phone', value: 'phone', sortable: false ,width: 150},
        { text: 'Created Time', value: 'time_create', sortable: false,width: 120 },
      ],
      tablePage:null,
      tableSort:null,
      tablePageCount:null,
      tableItemsPerPage:null,
      searchText:null,
      selectedAgent:null,
      selectedAgentUid:null,
    }),
    methods: {
      getChartData(){
        let self = this;
        HttpService.methods.get('http://callcentr.wellnessliving.com/report/missed')
          .then(function (response) {
            self.setChartData(response.data.diagrama)
            self.setTableData(response.data.calls);
            self.datePickerSetToday()
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      setChartData(data){
        var obj = {};
        this.serverChartData = data

        for (var i = 0; i < data.length; i++) {
          var name = data[i].full_name;
          var count = data[i].calls_count;
          obj[name] = count;
        }

        this.chartData = obj
      },
      getTableData(){
        var self = this;
        HttpService.methods.get('http://callcentr.wellnessliving.com/report/missed/call')
          .then(function (response) {
            let tableData = response.data.calls
            self.setTableData(tableData);
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      setTableData(data){
        this.tableCallsData=data.data;
        this.tablePage = parseInt(data.page);
        this.tablePageCount = data.pages_count;
      },
      getDate(timeStamp){
        var utc = new Date(timeStamp * 1000)
        var date = moment(utc).format('ll'); 
        var time = moment(utc).format('hh:mm:ss a');
        return time+" "+date
      },
      setDate(range){
        switch(range) {
          case 'today':  

            var today = new Date();
            var DD = today.getDate();
            var MM = today.getMonth() + 1; 
            var YYYY = today.getFullYear();

            if (DD < 10) {
              DD = '0' + DD;
            } 

            if (MM < 10) {
              MM = '0' + MM;
            } 

            today = YYYY + '-' + MM + '-' + DD;
            this.selectedDate = today;
            this.getDataByDate(this.selectedDate,'day')
            this.period='day';
          break;

          case 'day':
            this.getDataByDate(this.selectedDate,'day')
            this.period='day';
          break;

          case 'week':  
            this.getDataByDate(this.selectedDate,'week')
            this.period='week';
          break;

          case 'month':  
            this.getDataByDate(this.selectedDate,'month')
            this.period='month';
          break;

          case 'year': 
            this.getDataByDate(this.selectedDate,'year')
            this.period='year';
          break;
        }  
      },
      datePickerSetToday(){
        var today = new Date();
        var DD = today.getDate();
        var MM = today.getMonth() + 1; 
        var YYYY = today.getFullYear();

        if (DD < 10) {
          DD = '0' + DD;
        } 

        if (MM < 10) {
          MM = '0' + MM;
        } 

        today = YYYY+ '-' + MM+ '-' + DD 
        this.selectedDate = today;
        this.period='day';
      },
      setActive(ev){
        document.querySelector('.color-dark').classList.remove('color-dark');
        ev.target.classList.add('color-dark');
      },
      getDataByDate(startDate,period){
        var self = this

        HttpService.methods.get('http://callcentr.wellnessliving.com/report/missed/call/'+ startDate + '/' + period)
          .then(function (response) {
            let tableData = response.data.calls
            self.setTableData(tableData);
            self.setChartData(response.data.diagrama)
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      search(searchText){
        console.log(searchText);
        this.getDataByOptions();
      },
      getDataFromTableRow(){
        this.getDataByOptions()
      },
      getDataFromTableHead(head){
        console.log('table head',head)
      },
      getAgentFromChart(element){
        if(this.selectedAgentUid === this.serverChartData[element['index']]['uid']){
          this.selectedAgent = null;
          this.selectedAgentUid = null;
          this.getDataByOptions();
          return
        }

        if( element ){
          this.selectedAgent = this.serverChartData[element['index']];
          this.selectedAgentUid = this.serverChartData[element['index']]['uid'];
          this.getDataByOptions();
          return
        } 
      },
      getDataByOptions(){
        let self = this; 

        if(this.firstLoad){
          this.firstLoad = false
          return
        }  

        let startDate = this.selectedDate || '-' ;
        let period = this.period || '-' ;
        let uid = this.selectedAgentUid || '-';
        let searchWord = this.searchText || '-';
        let page = this.options.page || '-';
        var sortField = this.options.sortBy[0] || '-';
        
        if(this.options.sortBy[0] === 'user_data'){
          sortField = 'first_name'
        }
        if(this.options.sortBy[0] === 'business'){
          sortField = 'business_name'
        }
        if(this.options.sortBy[0] === 'contact'){
          sortField = 'contact'
        }

        var sortBy = this.options.sortDesc[0] || '-';
        if(this.options.sortDesc[0] === false){
          sortBy = 'ask'
        }
        if(this.options.sortDesc[0] === true){
          sortBy = 'desс'
        }

        HttpService.methods.get(
          'http://callcentr.wellnessliving.com/report/missed/call/'+
           startDate + '/' + 
           period + '/' + 
           uid + '/' + 
           searchWord + '/' + 
           sortField + '/' + 
           sortBy + '/' + 
           page
          )
          .then(function (response) {
            let tableData = response.data.calls
            self.setTableData(tableData);
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      changePage(page){
        this.options.page = page
        this.getDataByOptions()
      },
      goOutTo(string){
        console.log('authrize and goto',string)
      }
    },
    created: function(){
      this.getChartData();
      this.getTableData();
      this.setDate('today');
    },
    mounted () {
    },
  };
</script>
  
<style scoped lang="less">
  @import "../assets/less/main";
  .user{
    display:flex;
    p{
      margin-bottom:0
    }
    img{
      width: 25px;
      border-radius: 50%;
      margin-right: 5px;
    }
  }
  .dashboard{
    padding: 0 30px;
  }

  button{
    border-radius:2px;
    padding: 0 16px;
    font-family: Nunito;
    font-size: 14.4px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: normal;
    letter-spacing: normal;
    color: #ffffff;
    height: 36px;
  }

  #dashboard{
    width: 100%;
    background-color: #FAFBFE;
    .header-container{
      padding-bottom: 25px;
      h1{
        font-family: Nunito;
        font-size: 18px;
        font-weight: bold;
        font-stretch: normal;
        font-style: normal;
        line-height: 1.39;
        letter-spacing: normal;
        color: #6c757d;
        text-transform: normal
      }
    }
    .controls-container{
      padding-bottom:45px;
      display: flex;
      justify-content: space-between;
      .search-container{
        display: flex;
        #search{
          border: 1px solid #e8ecee;
          width: 264px;
          padding: 10px;
          border-radius: 2px;
          background-color: #f1f3fa;
          outline: none;
          height: 37px;
          padding: 0 0 0 40px;
          font-family: Nunito;
          font-size: 14.4px;
          font-weight: normal;
          font-stretch: normal;
          font-style: normal;
          line-height: normal;
          letter-spacing: normal;
          color: #adb5bd;
          position: relative;
          background: #F0F1F7;
          background-image: url(../assets/images/search_icon.png);
          background-repeat: no-repeat;
          background-position: 15px;
        }
        #search-button{
          margin-left:-20px;
          z-index:1
        }
      }
      .date-item{
        margin-right:4px !important;
      }
      .mx-datepicker{
          width:234px

      }
      .date-container {
        & /deep/ .mx-icon-calendar {
          background-color: #6421a7 !important;
          padding: 8px !important;
          width: 45px;
          right: 1px !important;
          svg {
            fill : white;
            margin-left: 6px;
          }
        }
      }

    }
    .chart-container{
      width: 100%;
      background-color: white;
      padding:24px;
      margin-bottom: 24px;
      box-shadow: 0 0 35px 0 rgba(154, 161, 171, 0.15);
      h2{
        font-family: Nunito;
        font-size: 14.4px;
        font-weight: bold;
        font-stretch: normal;
        font-style: normal;
        line-height: normal;
        letter-spacing: normal;
        color: #6c757d;
        padding-bottom:20px;
      }
      #chartId{
        height: 280px;
      }
    }
    .table-container{
      margin-bottom: 25px;
      /deep/ .v-data-table {
        th[aria-sort="ascending"]::after,
        th[aria-sort="ascending"]::after {
          opacity: 1;
          margin-left:5px;
          content: "\2191"; // up arrow
        }

        th[aria-sort="descending"]::after,
        th[aria-sort="descending"]::after {
          opacity: 1;
          margin-left:5px;
          content: "\2191"; // up arrow
        }

        thead tr th span{
          font-family: NunitoSans;
          font-size: 14px;
          font-weight: bold;
          line-height: 1.5;
          letter-spacing: normal;
          color: #6c757d;
        }
        .v-data-table__wrapper{
          max-height: 420px !important;
        }
      } 
      /deep/ .table-pagination{
        .v-pagination__item {
            background-color: #6C757D !important;
            border-color: #6C757D !important;
        }
      }
    }
    .fade-enter-active {
      transition: opacity 1s
    }

    .fade-enter,
    .fade-leave-active {
      opacity: 0
    }
  }
</style>
