<template>
    <!--http://callcentr.wellnessliving.com
    http://softphone-->
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
                        class="button button-li date-item color-grey"
                        v-on:click="setDate('today'); setActive($event)"
                    >Today
                    </button>
                    <button
                        class="button button-li date-item color-grey"
                        v-on:click="setBeforeDate(); setActive($event)"
                    > &#9666;
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
                        v-on:click="setNextDate(); setActive($event)"
                    > &#9656;
                    </button>
                    <button
                        class="button button-li date-item color-grey"
                        v-on:click="setDate('day'); setActive($event)"
                    >Day
                    </button>
                    <button
                        class="button button-li date-item color-grey color-dark"
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
            <div class="controls-container">
                <div>
                    <label class="typo__label">Select agents you need:</label>
                    <multiselect @close="setUsers" v-model="multiple_selected_value" :options="multiple_options" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick some" label="name" track-by="value" :preselect-first="true">
                        <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length && !isOpen">{{ values.length }} options selected</span></template>
                    </multiselect>
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
                    @close="ffFff"
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
                    @update:page="updatePage"
                    @update:sort-desc="updateSortDesc"
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
                    :next-icon="nextIcon"
                    :prev-icon="prevIcon"
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
  import Multiselect from 'vue-multiselect'

  export default {
    name: 'HelloWorld',
    components: {
      HeaderComponent,
      DatePicker,
      LineChart,
      Multiselect,
    },
    data: () => ({
      // firstLoad:true,
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
      multiple_value: null,
      multiple_options:[],
      multiple_selected_value:null,
      nextIcon: '>',
      prevIcon: '<',
      s_agent_id: '',
      period:'week',
    }),
    methods: {
      getDate(timeStamp){
        var utc = new Date(timeStamp * 1000)
        var date = moment(utc).format('ll');
        var time = moment(utc).format('hh:mm:ss a');
        return time+" "+date
      },
      setUsers(){
        this.selectedAgentUid = null;
        this.setDate(this.period);
      },
      ffFff(){
        console.log("!!!!!!!!");
      }
      ,
      setDate(range){

        this.period=range;
        this.searchText = '';
        switch(this.period) {
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
          default:
            this.period = 'week';
            break;
        }
      },
      setBeforeDate(){
        console.log("setBeforeDate "+this.period)
      },
      setNextDate(){
        console.log("setNextDate "+this.period)
      },
      datePickerSetDefaultPeriod(period){
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
        console.log(period);
        this.period=period;
      },
      setActive(ev){
        document.querySelector('.color-dark').classList.remove('color-dark');
        ev.target.classList.add('color-dark');
      },
      search(){
        console.log();
        this.getDataByOptions();
      },
      getAgentFromChart(element){
        if(this.selectedAgentUid === this.serverChartData[element['index']]['uid']){
          this.selectedAgent = null;
          this.selectedAgentUid = this.s_agent_id || null;
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
      changePage(page){
        this.options.page = page
        this.getDataByOptions()
      },
      goOutTo(string){
        console.log('authrize and goto',string)
      },
      updatePage(){
        this.getDataByOptions()
      },
      updateSortDesc(){
        this.getDataByOptions()
      },
      getChartData(){
        let self = this;
        this.$loading(true);
        HttpService.methods.get('http://softphone/report/missed')
        .then(function (response) {
          self.$loading(false);
          self.setChartData(response.data.diagrama);
          self.setTableData(response.data.calls);
          self.setMultiDropdown(response.data.agents);
          self.datePickerSetDefaultPeriod(self.period)
        })
        .catch(function (error) {
          console.log(error)
        })
      },
      getDataByOptions(){

        let self = this;

        self.generateSelectedAgentIdString();
        // if(this.firstLoad){
        //   this.firstLoad = false
        //   return
        // }

        let startDate = this.selectedDate || '-' ;
        let period = this.period || '-' ;
        let uid = this.selectedAgentUid || this.s_agent_id || '-';
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
          sortBy = 'asc'
        }

        if(this.options.sortDesc[0] === true){
          sortBy = 'desc'
        }

        this.$loading(true);
        HttpService.methods.get(
          'http://softphone/report/missed/call/'+
          startDate + '/' +
          period + '/' +
          uid + '/' +
          searchWord + '/' +
          sortField + '/' +
          sortBy + '/' +
          page
        )
        .then(function (response) {
          self.$loading(false);
          let tableData = response.data.calls
          self.setTableData(tableData);
        })
        .catch(function (error) {
          console.log(error)
        })
      },
      getDataByDate(startDate,period){
        var self = this
        self.generateSelectedAgentIdString();
        var ss_agent_id = '';
        if(self.s_agent_id !== '')
        {
          ss_agent_id = "/" + self.s_agent_id
        }
        this.$loading(true);
        HttpService.methods.get('http://softphone/report/missed/call/'+
          startDate + '/' + period + ss_agent_id)
        .then(function (response) {
          self.$loading(false);
          let tableData = response.data.calls
          self.setTableData(tableData);
          self.setChartData(response.data.diagrama)
        })
        .catch(function (error) {
          console.log(error)
        })
      },
      getTableData(){
        var self = this;
        HttpService.methods.get('http://softphone/report/missed/call')
        .then(function (response) {
          let tableData = response.data.calls
          self.setTableData(tableData);
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
      setTableData(data){
        this.tableCallsData=data.data;
        this.tablePage = parseInt(data.page);
        this.tablePageCount = data.pages_count;
      },
      setMultiDropdown(data){
        console.log(data);
        this.multiple_options = data;
      },
      generateSelectedAgentIdString () {
        console.log('generateSelectedAgentIdString');
        let s_agent_id = '';
        if(this.multiple_selected_value !== null)
        {
          let selected_agents_array = this.multiple_selected_value;
          let selected_agents_array_len = selected_agents_array.length;
          console.log(selected_agents_array_len);
          if(selected_agents_array_len)
          {
            for (let i = 0; i < selected_agents_array_len; i++){
              s_agent_id += selected_agents_array[i].value;
              if(i+1 !==  selected_agents_array_len){
                s_agent_id +=",";
              }
            }
          }
        }
       /* if(s_agent_id !== "")
        {
          //s_agent_id = "/" + s_agent_id;
          this.selectedAgentUid = null;
        }*/
        //console.log(s_agent_id);
        this.s_agent_id = s_agent_id;
      },
    },
    created: function(){
      this.getChartData();
    },
    mounted () {
    },
  };
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
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
                    content: "\2191";
                }

                th[aria-sort="descending"]::after,
                th[aria-sort="descending"]::after {
                    opacity: 1;
                    margin-left:5px;
                    content: "\2193";
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
