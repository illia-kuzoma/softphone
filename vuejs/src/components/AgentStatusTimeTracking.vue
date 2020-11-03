<template>
    <div id="Agent-Status">
        <div class="agent-status">

            <div class="controls-container">
                <div class="date-container">
                    <button
                        class="button button-li date-item color-grey"
                        v-on:click="setDate('today'); setActive($event)"
                    >Today
                    </button>
                    <button
                        class="button button-li date-item color-grey"
                        v-on:click="setBeforeDate(); setArrowActive($event)"
                    >&#9666;
                    </button>

                    <date-picker
                        v-model="selectedDate"
                        type="date"
                        class="date-item"
                        valueType="YYYY-MM-DD"
                        format="DD/MM/YYYY"
                        v-on:change="dateSelected()"
                    ></date-picker>

                    <button
                        class="button button-li date-item color-grey"
                        v-on:click="setNextDate(); setArrowActive($event)"
                    >&#9656;
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
                <button
                    class="button button-li date-item color-grey"
                    v-on:click="updateData();"
                    > Update Data
                </button>
            </div>

            <div class="controls-container multiselect-items">
                <div>
                    <label class="typo__label">Select Department(s)</label>
                    <multiselect
                        @close="setUsers"
                        v-model="department_multiple_selected_value"
                        :options="department_multiple_options"
                        :multiple="true"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :preserve-search="true"
                        placeholder="Pick some"
                        label="name"
                        track-by="value"
                        :preselect-first="true">
                            <template
                                slot="selection"
                                slot-scope="{ values, search, isOpen }">
                                    <span
                                        class="multiselect__single"
                                        v-if="values.length && !isOpen">
                                        {{ values.length }} options selected
                                    </span>
                                </template>
                    </multiselect>
                </div>
                <div>
                    <label class="typo__label">Select Team(s)</label>
                    <multiselect
                        @close="setUsers"
                        v-model="team_multiple_selected_value"
                        :options="team_multiple_options"
                        :multiple="true"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :preserve-search="true"
                        placeholder="Pick some"
                        label="name"
                        track-by="value"
                        :preselect-first="true">
                            <template
                                slot="selection"
                                slot-scope="{ values, search, isOpen }">
                                    <span
                                        class="multiselect__single"
                                        v-if="values.length && !isOpen">
                                        {{ values.length }} options selected
                                    </span>
                                </template>
                    </multiselect>
                </div>
                <div>
                    <label class="typo__label">Select agent(s)</label>
                    <multiselect
                        @close="setUsers"
                        v-model="agent_multiple_selected_value"
                        :options="agent_multiple_options"
                        :multiple="true"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :preserve-search="true"
                        placeholder="Pick some"
                        label="name"
                        track-by="value"
                        :preselect-first="true">
                            <template
                                slot="selection"
                                slot-scope="{ values, search, isOpen }">
                                    <span
                                        class="multiselect__single"
                                        v-if="values.length && !isOpen">
                                        {{ values.length }} options selected
                                    </span>
                                </template>
                    </multiselect>
                </div>
            </div>
            <!--   <div class="chart-container" v-if="Object.keys(chartData).length !== 0">
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
            </div> -->

            <div class="table-container" v-if='tableCallsData.length>0'>
                <v-data-table
                    :headers="tableCallsHeaders"
                    :items="tableCallsData"
                    :items-per-page="20"
                    :page.sync="tablePage"
                    :options.sync='optionsTable'
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
                    <template v-slot:item.user_data.department="{ item }">
                        <div class='user'>
                            <p>{{ item.user_data.department.name }}</p>
                        </div>
                    </template>
                    <template v-slot:item.user_data.team="{ item }">
                        <div class='user'>
                            <p>{{ item.user_data.team.name }}</p>
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

            <div class="table-container" v-if='tableTotalCallsData.length>0'>
                <h1>Total Table </h1>
                <v-data-table
                    :headers="tableTotalCallsHeaders"
                    :items="tableTotalCallsData"
                    :items-per-page="20"
                    :page.sync="tableTotalPage"
                    :options.sync='optionsTableTotal'
                    :hide-default-footer="true"
                    class="v-data-table elevation-1"
                    fixed-header
                    @update:page="updateTotalPage"
                    @update:sort-desc="updateTotalSortDesc"
                    >
                    <template v-slot:item.user_data="{ item }">
                        <div class='user'>
                            <img :src="item.user_data.photo_url" alt="">
                            <p>{{ item.user_data.full_name }}</p>
                        </div>
                    </template>
                    <template v-slot:item.user_data.department="{ item }">
                        <div class='user'>
                            <p>{{ item.user_data.department.name }}</p>
                        </div>
                    </template>
                    <template v-slot:item.user_data.team="{ item }">
                        <div class='user'>
                            <p>{{ item.user_data.team.name }}</p>
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
                    v-if="tableTotalPageCount>1"
                    v-model="tableTotalPage"
                    :length="tableTotalPageCount"
                    class="table-pagination"
                    @input="changeTotalPage"
                    :next-icon="nextIcon"
                    :prev-icon="prevIcon"
                ></v-pagination>
            </div>

            <div class="table-container" v-if='tableTotalCallsData.length==0'>
                <h1>No Table Data For This Period</h1>
            </div>

        </div>
    </div>
</template>

<script>
  import DatePicker from 'vue2-datepicker';
  import 'vue2-datepicker/index.css'
  // import LineChart from '@/components/Bar.vue'
  import HttpService from '@/services/HttpService'
  import moment from 'moment'
  import Multiselect from 'vue-multiselect'
  import router from '../router'

  export default {
    name: 'AgentStatus',
    components: {
      DatePicker,
      // LineChart,
      Multiselect,
    },
    data: () => ({
      // firstLoad:true,
      selectedDate:null,
      dateType:'date',
      chartData:[],
      userData:[],
      serverChartData:null,

      optionsTable: {},
      tableCallsData:[],
      tableCallsHeaders: [
        { text: 'Agent', value: 'user_data' },
        { text: 'Status Name', value: 'name' },
        { text: 'Status Value', value: 'value' },
        { text: 'Status In', value: 'time_start' },
        { text: 'Status Out', value: 'time_end' ,width: 200},
        { text: 'Status Duration', value: 'duration', sortable: false ,width: 150},
        // { text: 'Summary', value: 'duration', sortable: false,width: 120 },
      ],
      tablePage:null,
      tableSort:null,
      tablePageCount:null,
      // tableItemsPerPage:null,

      optionsTableTotal: {},
      tableTotalCallsData:[],
      tableTotalCallsHeaders: [
        { text: 'Day', value: 'day' },
        { text: 'Agent Name', value: 'user_data' },
        { text: 'Name', value: 'name' },
        { text: 'Value', value: 'value' },
        { text: 'Time', value: 'duration' },
      ],
      tableTotalPage:null,
      tableTotalSort:null,
      tableTotalPageCount:null,

// duration: ""
// name: "presence_status"
// time_end: null
// time_start: "2020-09-04 11:45:02"
// user_data: Object
// value: "ONLINE"

      searchText:null,
      selectedAgent:null,
      selectedAgentUid:null,
      //multiple_value: null,
      agent_multiple_options:[],
      agent_multiple_selected_value:null,
      department_multiple_options:[],
      department_multiple_selected_value:null,
      team_multiple_options:[],
      team_multiple_selected_value:null,
      nextIcon: '>',
      prevIcon: '<',
      s_agent_id: '',
      s_department_id:'',
      s_team_id:'',
      period:'week',
      // Use moment.js instead of the default
      /*momentFormat: {
        // Date to String
        stringify: (date) => {
          console.log(parent.parent.period);
          console.log(date);
          //console.log("period = "+ this.getPeriod());
          console.log(moment(date));
          console.log(moment(date).format('YYYY') );
          console.log(moment(date).format('MMMM') );
          console.log(moment(date).format('DD') );
          console.log(moment(date).format('dddd') );
          return date ? moment(date).format('MM/dd/YYY') : new Date()
        },
        // String to Date
        parse: (value) => {
          console.log("parse");
          console.log(value);
          let arr = value.split(' | ');
          console.log(arr[0]);
          console.log(arr[1]);
          let period = arr[1];
          let date = arr[0];
          console.log(date);
          let format =  'MM/dd/YYY';
          if(period === 'year')
          {
            format = "YYYY"
          }
          else if(period === 'month')
          {
            format = "MMMM/YYYY"
          }
          else if(period === 'week')
          {
            format = "w ddd YYYY"
          }
          else if(period === 'day')
          {
            format = "MMMM"
          }

          console.log(date);
          console.log(format);
          console.log(moment(date, format).toDate());
          return date ? moment(date, format).toDate() : null
        }
      },
      date_o:null*/
    }),
    methods: {
      getPeriod(){
        return this.period;
      },
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
      setDepartments(){
        this.selectedAgentUid = null;
        this.setDate(this.period);
      },
      setDate(range){

        console.log(localStorage.serve_host);

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
            console.log("today=" + today);
            this.selectedDate = today;
            this.getDataByDate(this.selectedDate,'day')
            this.period='day';
            break;

          case 'day':
            console.log("day=" + this.selectedDate);
            this.getDataByDate(this.selectedDate,'day')
            this.period='day';
            break;

          case 'week':
            console.log("week=" + this.selectedDate);
            this.getDataByDate(this.selectedDate,'week')
            this.period='week';
            break;

          case 'month':
            console.log("month=" + this.selectedDate);
            this.getDataByDate(this.selectedDate,'month')
            this.period='month';
            break;

          case 'year':
            console.log("year=" + this.selectedDate);
            this.getDataByDate(this.selectedDate,'year')
            this.period='year';
            break;
          default:
            this.period = 'week';
            break;
        }
      },
      setBeforeDate(){
        console.log("setBeforeDate "+this.period +" "+ this.selectedDate)
        let selected_date = moment(this.selectedDate, "YYYY-MM-DD").toDate()/*.add(1, 'days')*/;

        /*console.log(selected_date);
        let before_date = moment(moment(selected_date.setDate(selected_date.getDate()-1))
        .format("YYYY-MM-DD"), "YYYY-MM-DD").toDate();
        console.log(before_date);
        console.log(moment(selected_date.setDate(selected_date.getDate()-1))
        .format("YYYY-MM-DD"));*/
        /*console.log(before_date.getDay());
        console.log(before_date);
        let DD = before_date.getDay();
        let MM = before_date.getMonth() + 1;
        let YYYY = before_date.getFullYear();

        if (DD < 10) {
          DD = '0' + DD;
        }

        if (MM < 10) {
          MM = '0' + MM;
        }

        let s_tomorrow = YYYY + '-' + MM + '-' + DD;*/
        switch (this.period) {
          case 'today':
          case 'day':

            this.selectedDate = moment(selected_date.setDate(selected_date.getDate()-1))
            .format("YYYY-MM-DD")
            break;
            case "week":
              this.selectedDate = moment(selected_date.setDate(selected_date.getDate()-7))
              .format("YYYY-MM-DD")
              break;
          case 'month':
              this.selectedDate = moment(selected_date.setMonth(selected_date.getMonth()-1,selected_date.getDate()))
              .format("YYYY-MM-DD")
            break;
          case 'year':
            this.selectedDate = moment(selected_date.setFullYear(selected_date.getFullYear()-1,selected_date.getMonth(),selected_date.getDate()))
            .format("YYYY-MM-DD")
            break;
        }
        this.setDate(this.period)
      },
      setNextDate(){
        console.log("setNextDate "+this.period +" " + this.selectedDate)
        let selected_date = moment(this.selectedDate, "YYYY-MM-DD").toDate()/*.add(1, 'days')*/;
        switch (this.period) {
          case 'today':
          case 'day':

            this.selectedDate = moment(selected_date.setDate(selected_date.getDate()+1))
            .format("YYYY-MM-DD")
            break;
          case "week":
            this.selectedDate = moment(selected_date.setDate(selected_date.getDate()+7))
            .format("YYYY-MM-DD")
            break;
          case 'month':
            this.selectedDate = moment(selected_date.setMonth(selected_date.getMonth()+1,selected_date.getDate()))
            .format("YYYY-MM-DD")
            break;
          case 'year':
            this.selectedDate = moment(selected_date.setFullYear(selected_date.getFullYear()+1,selected_date.getMonth(),selected_date.getDate()))
            .format("YYYY-MM-DD")
            break;
        }
        this.setDate(this.period)
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
        // console.log("datePickerSetDefaultPeriod="+period);
        this.period=period;
      },
      setArrowActive(ev){
        let f = document.querySelector('.color-blue');
        if(f !== null)
        {
          f.classList.remove('color-blue');
        }
        // console.log('setArrowActive');
        ev.target.classList.add('color-blue');
      },
      setActive(ev){
        document.querySelector('.color-dark').classList.remove('color-dark');
        ev.target.classList.add('color-dark');
        let f = document.querySelector('.color-blue');
        if(f !== null)
        {
          f.classList.remove('color-blue')
        }
      },
      search(){
        // console.log();
        this.getDataByOptions({url:'status'});
      },
      getAgentFromChart(element){
        if(this.selectedAgentUid === this.serverChartData[element['index']]['uid']){
          this.selectedAgent = null;
          this.selectedAgentUid = this.s_agent_id || null;
          this.getDataByOptions({});
          return
        }

        if( element ){
          this.selectedAgent = this.serverChartData[element['index']];
          this.selectedAgentUid = this.serverChartData[element['index']]['uid'];
          this.getDataByOptions({});
          return
        }
      },
      changePage(page){
        this.optionsTable.page = page
        this.getDataByOptions({url:'status'})
      },
      changeTotalPage(page){
        this.optionsTableTotal.page = page
        this.getDataByOptions({url:'total'})
      },
      goOutTo(string){
        console.log('authrize and goto',string)
      },
      updatePage(){
        this.getDataByOptions({url:'status'})
      },
      updateTotalPage(){
        this.getDataByOptions({url:'total'})
      },
      updateSortDesc(){
        this.getDataByOptions({url:'status'})
      },
      updateTotalSortDesc(){
        this.getDataByOptions({url:'total'})
      },
      tokenIsCorrect(token){
        return !(!token || token === '-' || token === undefined);
      },
      getReportData(refresh = false){
        let self = this;

        let token = localStorage.token ;

        if(!self.tokenIsCorrect(token))
        {
          router.push('/')
        }
        else {
          this.$loading(true);
          HttpService.methods.get('/report/agent/status/'+(refresh?'refresh/':'')+token)
          .then(function (response) {
            console.log('getReportData',response)
            self.$loading(false);
            if(response.data.error===true){
              localStorage.token = '';
              self.isValid = true
              alert(response.data.message)
            }
            // below 2 ways to set data to header
            self.$store.state.user = response.data.user;
            self.userData = response.data.user;

            // self.setChartData(response.data.diagrama);
            self.setTableData(response.data.status);
            self.setTableTotalData(response.data.total);
            self.setAgentMultiDropdown(response.data.agents);
            self.setDepartmentMultiDropdown(response.data.departments);
            self.datePickerSetDefaultPeriod(self.period)
          })
          .catch(function (error) {
            self.errorHappen(error);
            if(!self.tokenIsCorrect(token))
            {
              router.push('/')
            }
          })
        }
      },
      getDataByOptions(options){
        let self = this;

        this.generateSelectedAgentIdString();
        this.generateSelectedDepartmentIdString();
        this.generateSelectedTeamIdString();
        // if(this.firstLoad){
        //   this.firstLoad = false
        //   return
        // }

        let url = '/report/agent/status/';
        let startDate = this.selectedDate || '-' ;
        let period = this.period || '-' ;
        let department = this.s_department_id || '-';
        let team = this.s_team_id || '-';
        let uid = this.selectedAgentUid || this.s_agent_id || '-';
        let searchWord = this.searchText || '-';

        let page =  '-';
        let sortField =  '-';
        let sortBy = '-';

console.log("options.url = "+options.url);
        if(options.url === 'status'){
          url = '/report/agent/status/page/';
          page = this.optionsTable.page;
          sortField = this.optionsTable.page;
          sortBy = this.optionsTable.page;
        }

        //console.log("sortField1 = "+sortField);
        if(options.url === 'total'){
          url = '/report/agent/status/total/page/';
          page = this.optionsTableTotal.page;
          sortField = this.optionsTableTotal.page;
          sortBy = this.optionsTableTotal.page;
        }
        //console.log("sortField2 = "+sortField);

        if(this.optionsTable.sortBy[0] === 'user_data'){
          sortField = 'first_name'
        }
        if(this.optionsTable.sortBy[0] === 'business'){
          sortField = 'business_name'
        }
        if(this.optionsTable.sortBy[0] === 'contact'){
          sortField = 'contact'
        }
        if(this.optionsTable.sortBy[0] === 'user_data.department'){
          sortField = 'department_name'
        }
        if(this.optionsTable.sortBy[0] === 'user_data.team'){
          sortField = 'team_name'
        }
        if(this.optionsTable.sortDesc[0] === false){
          sortBy = 'asc'
        }
        if(this.optionsTable.sortDesc[0] === true){
          sortBy = 'desc'
        }

        if(this.optionsTableTotal.sortBy[0] === 'day'){
          sortField = 'day'
        }
        if(this.optionsTableTotal.sortBy[0] === 'user_data'){
          sortField = 'first_name'
        }
        if(this.optionsTableTotal.sortBy[0] === 'name'){
          sortField = 'name'
        }
        if(this.optionsTableTotal.sortBy[0] === 'value'){
          sortField = 'value'
        }
        if(this.optionsTableTotal.sortBy[0] === 'duration'){
          sortField = 'duration'
        }
        if(this.optionsTableTotal.sortDesc[0] === false){
          sortBy = 'asc'
        }
        if(this.optionsTableTotal.sortDesc[0] === true){
          sortBy = 'desc'
        }
        console.log("sortField3 = "+sortField);

        this.$loading(true);
        console.log("options");
        console.log(options);

        console.log('Object.keys(options).length='+Object.keys(options).length)

        if(Object.keys(options).length === 0){
          HttpService.methods.get(
            '/report/agent/status/'+
            startDate + '/' +
            period + '/' +
            department + '/' +
            team + '/' +
            uid + '/' +
            searchWord + '/' +
            sortField + '/' +
            sortBy + '/' +
            page
          )
          .then(function (response) {
            self.$loading(false);

              console.log('getDataByOptions status', options);
              // console.log(tableData);
              let tableData = response.data.status;
              self.setTableData(tableData);

            // self.setChartData(response.data.diagrama)
          })
          .catch(function (error) {
            self.errorHappen(error)
          })

          HttpService.methods.get(
            '/report/agent/status/total/page/'+
            startDate + '/' +
            period + '/' +
            department + '/' +
            team + '/' +
            uid + '/' +
            searchWord + '/' +
            sortField + '/' +
            sortBy + '/' +
            page
          )
          .then(function (response) {
            self.$loading(false);

              console.log('getDataByOptions total', options);
              let tableTotalData = response.data.total;
              self.setTableTotalData(tableTotalData);
            // self.setChartData(response.data.diagrama)
          })
          .catch(function (error) {
            self.errorHappen(error)
          })

        } else {

          HttpService.methods.get(
            // '/report/agent/status/page/'+
            url +
            startDate + '/' +
            period + '/' +
            department + '/' +
            team + '/' +
            uid + '/' +
            searchWord + '/' +
            sortField + '/' +
            sortBy + '/' +
            page
          )
          .then(function (response) {
            self.$loading(false);

            if(response.data.status){
              console.log('getDataByOptions status', options);
              // console.log(tableData);
              let tableData = response.data.status;
              self.setTableData(tableData);
            }

            if(response.data.total){
              console.log('getDataByOptions total', options);
              let tableTotalData = response.data.total;
              self.setTableTotalData(tableTotalData);
            }
            // self.setChartData(response.data.diagrama)
          })
          .catch(function (error) {
            self.errorHappen(error)
          })
        }
      },
      getDataByDate(startDate,period){
        let self = this
        self.generateSelectedAgentIdString();
        self.generateSelectedDepartmentIdString();
        self.generateSelectedTeamIdString();
        var ss_agent_id = '';
        if(self.s_agent_id !== '')
        {
          ss_agent_id = "/" + self.s_agent_id
        }
        let department = this.s_department_id || '-';
        let team = this.s_team_id || '-';
        this.$loading(true);
        HttpService.methods.get('/report/agent/status/page/'+
          startDate + '/' + period + '/' + department  + '/' + team + ss_agent_id)
        .then(function (response) {
          console.log('getDataByDate',response)
          self.$loading(false);
          let tableData = response.data.status;
          // console.log(tableData);
          // console.log(tableTotalData);
          self.setTableData(tableData);
          // self.setChartData(response.data.diagrama)
          self.setTeamMultiDropdown(response.data.teams)
          self.setAgentMultiDropdown(response.data.agents);
        })
        .catch(function (error) {
          self.errorHappen(error);
        });
        HttpService.methods.get('/report/agent/status/total/page/'+
          startDate + '/' + period + '/' + department  + '/' + team + ss_agent_id)
        .then(function (response) {
          console.log('getDataByDate',response)
          self.$loading(false);
          let tableTotalData = response.data.total;
          self.setTableTotalData(tableTotalData);
        })
        .catch(function (error) {
          self.errorHappen(error);
        })
      },
      // getTableData(){
      //   var self = this;
      //   HttpService.methods.get('/report/agent/status')
      //   .then(function (response) {
      //     console.log(response)
      //     let tableData = response.data.calls
      //     self.setTableData(tableData);
      //   })
      //   .catch(function (error) {
      //     self.errorHappen(error);
      //   })
      // },
      // setChartData(data){
      //   var obj = {};
      //   this.serverChartData = data

      //   for (var i = 0; i < data.length; i++) {
      //     var name = data[i].full_name;
      //     var count = data[i].calls_count;
      //     obj[name] = count;
      //   }

      //   this.chartData = obj
      // },
      setTableData(data){
        console.log('setTableData',data)
        this.tableCallsData=data.data;
        this.tablePage = parseInt(data.page);
        this.tablePageCount = data.pages_count;
        // console.log(this.tableCallsData)
        // console.log(this.tablePage)
        // console.log(this.tablePageCount)
      },
            // },
      setTableTotalData(data){
        console.log('setTableData Total',data)
        this.tableTotalCallsData=data.data;
        this.tableTotalPage = parseInt(data.page);
        this.tableTotalPageCount = data.pages_count;
        // console.log(this.tableCallsData)
        // console.log(this.tablePage)
        // console.log(this.tablePageCount)
      },
      setAgentMultiDropdown(data){
        //console.log(data);
        this.agent_multiple_options = data;
      },
      setDepartmentMultiDropdown(data){
        // console.log(data);
        this.department_multiple_options = data;
      },
      setTeamMultiDropdown(data){
        console.log(data);
        this.team_multiple_options = data;
      },
      generateSelectedAgentIdString () {
        console.log('generateSelectedAgentIdString');
        let s_agent_id = '';
        if(this.agent_multiple_selected_value !== null)
        {
          let selected_agents_array = this.agent_multiple_selected_value;
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
      generateSelectedDepartmentIdString()
      {
        console.log('generateSelectedDepartmentIdString');
        let s_department_id = '';
        if(this.department_multiple_selected_value !== null)
        {
          let selected_departments_array = this.department_multiple_selected_value;
          let selected_departments_array_len = selected_departments_array.length;
          console.log(selected_departments_array_len);
          if(selected_departments_array_len)
          {
            for (let i = 0; i < selected_departments_array_len; i++){
              s_department_id += selected_departments_array[i].value;
              if(i+1 !==  selected_departments_array_len){
                s_department_id +=",";
              }
            }
          }
        }
        this.s_department_id = s_department_id;
      },
      generateSelectedTeamIdString()
      {
        console.log('generateSelectedTeamIdString');
        let s_team_id = '';
        if(this.team_multiple_selected_value !== null)
        {
          let selected_teams_array = this.team_multiple_selected_value;
          let selected_teams_array_len = selected_teams_array.length;
          console.log(selected_teams_array_len);
          if(selected_teams_array_len)
          {
            for (let i = 0; i < selected_teams_array_len; i++){
              s_team_id += selected_teams_array[i].value;
              if(i+1 !==  selected_teams_array_len){
                s_team_id +=",";
              }
            }
          }
        }
        this.s_team_id = s_team_id;
      },
      dateSelected(){
        console.log('dateSelected +');
        console.log(this.selectedDate);
        this.getDataByDate(this.selectedDate,this.period)
          //this.setDate(this.period);
         // this.selectedDate= this.selectedDate + " | " + this.period
      },
      updateData(){
        this.getReportData(true)
      },
      errorHappen(error){
        console.log('error : ',error);
        alert('Something went wrong!');
        this.$loading(false);
        this.getReportData();
      }
    },
    created: function(){
      this.getReportData();
    },
    mounted () {
    }
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

    #Agent-Status{
        width: 100%;
        background-color: #FAFBFE;
        .agent-status{
            margin-top: 30px;
            // padding: 0 30px;
        }
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
        .multiselect-items{
            justify-content: flex-start;
            & :not(:last-child){
                margin-right: 20px;
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
