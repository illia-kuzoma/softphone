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
                v-on:click="search(searchText)"
                >Search
              </button>
          </div>
        </div>

        <div class="chart-container" v-if="chartData">
          <h2>MISSED CALLS</h2>
          <line-chart
            id="chartId"
            v-if='chartData'
            :chartObject='chartData'
            @clicked="getElemenFromChart"
            class="chart">
          </line-chart>
        </div>

        <div class="table-container" v-if='tableCallsData'>
          <v-data-table
            :headers="tableCallsHeaders"
            :items="tableCallsData"
            :items-per-page="20"
            :page.sync="tablePage"
            :hide-default-footer="true"
            class="v-data-table"
            @click:row="getDataFromTableRow"
            fixed-header
            >
              <template v-slot:item.user_data="{ item }">
                <div class='user'>
                  <img :src="item.user_data.photo_url" alt="">
                  <p>{{ item.user_data.full_name }}</p>
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
          ></v-pagination>
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
      selectedDate:null,
      dateType:'date',
      chartData:null,
      tableCallsData:null,
      serverChartData:null,
      // uid: (...)
      // photo_url: (...)
      // first_name: (...)
      // last_name: (...)
      // business_name: (...)
      // contact: (...)
      // priority: (...)
      // phone: (...)
      // time_create: 
      tableCallsHeaders: [
          // { text: 'Type', value: 'type' },
          { text: 'Agent', value: 'user_data' },
          { text: 'Business Name', value: 'business_name' },
          { text: 'Contact', value: 'contact' },
          { text: 'Priority', value: 'priority', sortable: false},
          { text: 'Phone', value: 'phone', sortable: false },
          { text: 'Created Time', value: 'time_create', sortable: false },
      ],
      tablePage:null,
      tablePageCount:null,
      tableItemsPerPage:null,
      searchText:null,
    }),
    methods: {
      getChartData(){
        let self = this;
        HttpService.methods.get('http://callcentr.wellnessliving.com/report/missed')
          .then(function (response) {
            self.setChartData(response.data.diagrama)
            console.log('1getChartData',response)
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      setChartData(data){
        var obj = {};
        this.serverChartData = data

        for (var i = 0; i < data.length; i++) {
          var name = data[i].first_name + ' ' + data[i].first_name
          var count = data[i].calls_count
          obj[name]=count
        }
        this.chartData = obj
      },
      getTableData(){
        var self = this;
        HttpService.methods.get('http://callcentr.wellnessliving.com/report/missed/call')
          .then(function (response) {
            let tableData = response.data.calls
            self.setTableData(tableData);
            console.log('2getChartData',response)
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      setTableData(data){
        this.tableCallsData=data.data;
        this.tablePage = data.page;
        this.tablePageCount = data.pages_count;
        this.tableCallsData=[
          {
            'uid': "1",
            'business_name': "Bavaria Motors LLC",
            'contact': "UFO",
            'priority': "low",
            'phone': "+380508008080",
            'time_create': "1579996800",
            'user_data':{
                'photo_url': "https://www.bmw-motorsport.com/content/dam/bmw/marketBMWSPORTS/bmw-motorsport_com/assets/bmw-m-motorsport/race-cars/bmw-m2-cs-racing/bmw-m2-cs-racing-ascari-hotspot.jpg",
                'first_name': "Ivan",
                'last_name': "Petrov",
                'full_name': "Ivan Petrov"
              }
          },
          {
            'uid': "12",
            'business_name': "Bavaria Motors LLC",
            'contact': "UFO",
            'priority': "low",
            'phone': "+380508008080",
            'time_create': "1579996800",
            'user_data':{
                'photo_url': "https://www.bmw-motorsport.com/content/dam/bmw/marketBMWSPORTS/bmw-motorsport_com/assets/bmw-m-motorsport/race-cars/bmw-m2-cs-racing/bmw-m2-cs-racing-ascari-hotspot.jpg",
                'first_name': "Ivan",
                'last_name': "Petrov",
                'full_name': "Ivan Petrov"
              },
          },
          {
            'uid': "13",
            'business_name': " Motors LLC",
            'contact': "U2O",
            'priority': "low",
            'phone': "+380508008222",
            'time_create': "1571296800",
            'user_data':{
                'photo_url': "https://www.bmw-motorsport.com/content/dam/bmw/marketBMWSPORTS/bmw-motorsport_com/assets/bmw-m-motorsport/race-cars/bmw-m2-cs-racing/bmw-m2-cs-racing-ascari-hotspot.jpg",
                'first_name': "Ivan1111",
                'last_name': "Petrov1111",
                'full_name': "Ivan Petrov1111"
              },
          },

        ]
      },
      getDate(timeStamp){
        var utc = new Date(timeStamp * 1000)
        var date = moment(utc).format('ll'); 
        var time = moment(utc).format('hh:mm:ss a');
        return time+" "+date
      },
      setDate(range){
        console.log(range) 
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

            today = YYYY+ '-' + MM+ '-' + DD 
            this.selectedDate = today;
            this.getDataByDate(this.selectedDate,'day')
          break;

          case 'day':
            this.getDataByDate(this.selectedDate,'day')
          break;

          case 'week':  
            this.getDataByDate(this.selectedDate,'week')
          break;

          case 'month':  
            this.getDataByDate(this.selectedDate,'month')
          break;

          case 'year': 
            this.getDataByDate(this.selectedDate,'year')
          break;
        }  
      },
      setActive(ev){
        document.querySelector('.color-dark').classList.remove('color-dark');
        ev.target.classList.add('color-dark');
      },
      getDataByDate(date,period){
        var self = this
        HttpService.methods.get('http://callcentr.wellnessliving.com/report/missed/call/'+ date+ '/'+ period)
          .then(function (response) {
            let tableData = response.data.calls
            self.setTableData(tableData);
            console.log('3getDataByDate',response)
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      search(searchText){
        console.log(searchText)
      },
      getDataFromTableRow(element){
        console.log('table elem',element)
      },
      getElemenFromChart(element){
        console.log('1Chart elem',element)
        console.log('2serverChartData',this.serverChartData)

        console.log('3chartData',this.chartData)
        console.log('!!!serverChartData',this.serverChartData[element['index']])
      },
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
        .v-data-table__wrapper{
          max-height: 420px !important;
        //  overflow-y: auto;
        }
      } 
      /deep/ .table-pagination{
         //.v-application .primary {
         .v-pagination__item {
            background-color: #6C757D !important;
            border-color: #6C757D !important;
        }


      }
    }
  }
</style>
