<template>
  <!-- <div class='holder'> -->
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
              class="button date-item color-dark"
              v-on:click="getTodayDate('today')"
              >Today
            </button>

              <!-- valueType='date' -->
              <!-- format="YYYY/MM/DD" -->
           
            <!-- <date-picker 
              v-model="selectedDate"
              type="month"
              valueType='month'
              format="MM"
              class="date-item"
              @change="setDateRange"
            ></date-picker> -->

            <date-picker 
              v-if='dateType==="date"'
              v-model="selectedDate"
              type="date"
              class="date-item"
              format="DD/MM/YYYY"
              @change="setDateRange('day',selectedDate)"
            ></date-picker>
              <!-- valueType='date' -->

            <date-picker 
              v-if='dateType==="week"'
              v-model="selectedWeek"
              type="week"
              class="date-item"
              token='w'
              @change="setDateRange('week',selectedWeek)"
            ></date-picker>
              <!-- valueType='date' -->

            <date-picker 
              v-if='dateType==="month"'
              v-model="selectedMonth"
              type="month"
              class="date-item date-picker "
              @change="setDateRange('month',selectedMonth)"
            ></date-picker>
              <!-- valueType='date' -->
              <!-- token='w' -->

            <date-picker 
              v-if='dateType==="year"'
              v-model="selectedYear"
              class="date-item"
              type="year"
              @change="setDateRange('year',selectedYear)"
            ></date-picker>
              <!-- valueType='date' -->
              <!-- token='YYYY' -->





            <button class="button date-item color-dark" v-on:click="getTodayDate('day')" >Day</button>
            <button class="button date-item color-grey" v-on:click="getTodayDate('week')" >Week</button>
            <button class="button date-item color-grey" v-on:click="getTodayDate('month')" >Month</button>
            <button class="button date-item color-grey" v-on:click="getTodayDate('year')" >Year</button>
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

        <div class="chart-container">
          <h2>MISSED CALLS</h2>
          <line-chart
            id="chartId" 
            v-if='chartData'
            :chartObject='chartData'
            class="chart">
          </line-chart>
        </div>

        <div class="table-container">
          <v-data-table
            :headers="tableCallsHeaders"
            :items="tableCallsData"
            :items-per-page="20"
            :page.sync="tablePage"
            :hide-default-footer="true"
            class="v-data-table"
            fixed-header 
            >
            <!-- :height="300" -->
            <!--   <template slot="items" slot-scope="props">
                <td>{{ props.index }}</td>
                <td>{{ props.item.name }}</td>
                <td>{{ getProject(props.item.project_uuid) }}</td>
                <td>{{ props.item.deadline }}</td>
                <td>{{ getUser(props.item.executor) }}</td>
                <td>{{ getUser(props.item.creator) }}</td>
                <td>{{ props.item.description }}</td>
              </template> -->
              <!-- <template v-slot:item.type="{ item }">
                <p>
                  {{ item["type"] }}
                </p>
              </template>
              <template v-slot:item.created="{ item }">
                <p v-if='item["created"]'>
                  {{getDate(item["created"])}}
                </p>
              </template>
              <template v-slot:item.status="{ item }">
                <p v-if='item["status"] === 0'>CREATED</p>
                <p v-if='item["status"] === 1'>EXTRACTING</p>
                <p v-if='item["status"] === 2'>EXTRACTED</p>
                <p v-if='item["status"] === 3'>SCANNING</p>
                <p v-if='item["status"] === 4'>SCANNED</p>
                <p v-if='item["status"] === 5'>FAILED</p>
              </template>
              <template v-slot:item.sample="{ item }">
                <input
                  type="checkbox"
                  v-model="item.sample"
                  true-value="true"
                  false-value="false"
                  @click="updateData(item)"
                >
              </template>
              <template v-slot:item.policies="{ item }">
                 
                <div @click="setDs(item)" > 
                  <v-select
                    v-model="item.policy"
                    :items="policies"
                    v-if="policies"
                    label="Select policy"
                    class='multi_drop'
                    item-text="name"
                    item-value="slug"
                    multiple
                    @input="updateItem"
                  >
                  </v-select>
                </div>
              </template>
              <template v-slot:item.action="{ item }">
                <v-icon
                  small
                  class="edit"
                  @click="goTo(`/data-secrets/edit/${item.id}`)"
                >
                </v-icon>
                <v-icon
                  small
                  class="delete"
                  @click="deleteItem(item)"
                >
                </v-icon>
              </template>
              <template v-slot:item.button="{ item }">
                <button
                  :disabled="clicked.includes(item.id)"
                  v-if='item["status"] === 0'
                  class='button-secondary start'
                  :class="{'active':!item.loading,
                           'button-secondary-loading':item.loading}
                           "
                  @click="start(item);
                          item.loading =! item.loading">

                  START
                  <div class="lds-ellipsis"
                    v-if='item["status"] === 0'
                    v-show="item.loading && clicked.includes(item.id)">
                    <div></div><div></div><div></div><div></div>
                  </div>
                </button>
                <button
                  disabled
                  v-if='item["status"] === 1 || item["status"] === 2 || item["status"] === 3'
                  class='button-secondary-disabled'
                  >
                  View Analytics
                </button>
                <button
                  v-if='item["status"] === 4'
                  class='button-secondary'
                  @click="goTo(`/data-secrets/analytics/${item.id}`,item)"
                >
                  View Analytics
                </button>
                <button
                  :disabled="clicked.includes(item.id)"
                  v-if='item["status"] === 5'
                  class='button-secondary restart '
                  :class="{'active':!item.loading,
                           'button-secondary-loading':item.loading}"
                  @click="start(item);
                          item.loading =! item.loading">
                  RESTART
                  <div class="lds-ellipsis"
                    v-if='item["status"] === 5'
                    v-show="item.loading && clicked.includes(item.id)">
                    <div></div><div></div><div></div><div></div>
                  </div>
                </button>
              </template> -->
          </v-data-table>
          <v-pagination
            v-model="tablePage"
            :length="tablePageCount"
            class="table-pagination"
          ></v-pagination>
        </div>
      </div>
    </div>
  <!-- </div> -->
</template>

<script>
  import HeaderComponent from '@/components/header.vue'
  import DatePicker from 'vue2-datepicker';
  import 'vue2-datepicker/index.css'
  import LineChart from '@/components/Bar.vue'
  import HttpService from '@/services/HttpService'

  export default {
    name: 'HelloWorld',
    components: { 
      HeaderComponent,
      DatePicker,
      LineChart,
    },
    data: () => ({
selectedDate:null,
selectedWeek:null,
selectedMonth:null,
selectedYear:null,
      dateType:'date',
      chartData:null,
      tableCallsData:null,
      tableCallsHeaders: [
          { text: 'Type', value: 'type' },
          { text: 'Agent', value: 'agent' },
          { text: 'Business Name', value: 'business' },
          { text: 'Contact', value: 'contact' },
          { text: 'Priority', value: 'priority', sortable: false},
          { text: 'Phone', value: 'phone', sortable: false },
          { text: 'Created Time', value: 'created', sortable: false },
      ],
      tablePage:null,
      tablePageCount:null,
      tableItemsPerPage:null,
      searchText:null,
    }),
    methods: {
      showModal(){
      },
      getChartData(){
        // http://callcentr.wellnessliving.com/report/missed
        // http://callcentr.wellnessliving.com/report/missed/call
             // let self = this;
        HttpService.methods.get('http://callcentr.wellnessliving.com/report/missed')
          .then(function (response) {
            console.log('getChartData',response)
            // self.login(true);
          })
          .catch(function (error) {
            console.log(error)
            // self.login(false);
          })

        this.chartData = {
          'Вачовски': 20,
          'Джоэл Сильвер': 90 ,
          'иану Ривз': 33,
          'Лоуренс Фишборн':23,
          'Керри-Энн Мосс': 31,
          'Хьюго Уивинг':76,
          'Джо Пантолиано':45,
          'Билл Поуп':33,
          'Дон Дэвис':5,
          'Warner Bros.':15,
          '1Братья Вачовски': 20,
          '1Джоэл Сильвер': 90 ,
          '1иану Ривз': 33,
          '1Лоуренс Фишборн':23,
          '1Керри-Энн Мосс': 31,
          '1Хьюго Уивинг':76,
          '1Джо Пантолиано':45,
          '1Билл Поуп':33,
          '1Дон Дэвис':5,
          '22Warner Bros.':15,
          '2Братья Вачовски': 20,
          '2Джоэл Сильвер': 90 ,
          '2иану Ривз': 33,
          '2Лоуренс Фишборн':23,
          '2Керри-Энн Мосс': 31,
          '2Хьюго Уивинг':76,
          '2Джо Пантолиано':45,
          '2Билл Поуп':33,
          '2Дон Дэвис':5,
          '2Warner Bros.':15,
        }
      },
      getTableData(){
        HttpService.methods.get('http://callcentr.wellnessliving.com/report/missed/call')
          .then(function (response) {
            console.log('getTableData',response)
            // self.login(true);
          })
          .catch(function (error) {
            console.log(error)
            // self.login(false);
          })
        // http://callcentr.wellnessliving.com/report/missed
        // http://callcentr.wellnessliving.com/report/missed/call
        this.tableCallsData=[
          {
            type:'1',
            agent:'123',
            business:'123',
            contact:'123',
            priority:'123',
            phone:'123',
            created:'123',
          },
          {
            type:'2',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'3',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'4',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'5',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'6',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'7',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'8',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'9',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'10',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'11',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'12',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'13',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'14',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'15',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'16',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'17',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'18',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'19',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'20',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'21',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'22',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'23',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'24',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },
          {
            type:'25',
            agent:'234',
            business:'234',
            contact:'234',
            priority:'234',
            phone:'234',
            created:'234',
          },

        ]
        this.tablePage = 1;
        this.tablePageCount = 2;
        // this.tableItemsPerPage = 10;
      },
      getTodayDate(range){
        console.log(this.selectedDate)
        switch(range) {
          case 'today':  
            this.dateType='date'
            this.selectedDate = new Date();
          break;

          case 'day':
            this.dateType='date'
            // this.selectedDate = new Date(new Date().setDate(new Date().getDate() - 1));
          break;

          case 'week':  
            this.dateType='week'

            // this.selectedDate = new Date(new Date().setDate(new Date().getDate() - 7));
          break;

          case 'month':  
            this.dateType='month'

            // this.selectedDate = new Date(new Date().setMonth(new Date().getMonth() - 1));
          break;

          case 'year': 
            this.dateType='year'

            // this.selectedDate = new Date(new Date().setFullYear(new Date().getFullYear() - 1 ));
          break;
        }  
      },
      setDateRange(range,selectedDate){
        console.log('string',range)
        console.log('selectedDate',selectedDate)
        let date = null
        switch(range) {
          case 'today':  
            // this.dateType='date'
            // this.selectedDate = new Date();
          break;

          case 'day':
            // this.dateType='date'
            // this.selectedDate = new Date(new Date().setDate(new Date().getDate() - 1));
          break;

          case 'week':  
            // this.dateType='week'
            date = selectedDate.getDate() +1;
            console.log(this.selectedWeek,date)
            // this.selectedDate = new Date(new Date().setDate(new Date().getDate() - 7));
          break;

          case 'month':  
            date = selectedDate.getMonth() +1;
            console.log(date)
          break;

          case 'year': 
            date = selectedDate.getFullYear() ;
            console.log(date)
          break;
        }  

      },
      search(searchText){
        console.log(searchText)
      }

    },
    created: function(){
      this.getChartData();
      this.getTableData();
      this.getTodayDate('today');
    },
    mounted () {
      // this.$refs.canvas.onclick = function(evt) {
      //   console.log(evt)
      //     // this.$data.chartId.getElementsAtEvent(evt);
      // }
    },
  };
</script>
  
<style scoped lang="less">
  @import "../assets/less/main";

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
      /deep/ .v-data-table {




        .v-data-table__wrapper{
          height: 420px !important;
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
