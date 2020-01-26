<script>
  import { Bar } from 'vue-chartjs'

  export default {
    extends: Bar,
    props: ['chartObject'],
    data: () => ({
      gradient: null,
      type: 'horizontalBar',
      chartdata: {
        labels: [],
        datasets: [{
            label: 'MISSED CALLS',
            // backgroundColor: "#727cf5",
            // backgroundColor: ["rgba(197, 197, 197, 0.1)"],
            // borderColor: '#FE7325',
            // borderWidth: 2,
            // backgroundColor: '#f87979',
            // fillColor : "rgba(151,187,205,0.5)",
            // strokeColor : "rgba(151,187,205,1)",
            // pointColor : "rgba(151,187,205,1)",
             // pointStrokeColor : "#fff",
             // borderWidth: 1,
            // backgroundColor: this.gradient,
            data: []
        }],
        chartObject:null,
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: false,
        },
        scales: {
          yAxes: [
            {
              display: true,
                // zeroLineWidth: 2,
              gridLines: {
                display: false,
              }
            },
          ],
          xAxes: [{
            gridLines: {
              display: false,
              // zeroLineWidth: 2
            }
          }],
        },

        // events: ['click'],
        // maintainAspectRatio: true,
        // onClick: function(evt) {
        //     console.log(evt);
        // }
        // onClick: function(event){
        //   console.log(event)
        // }
      }
    }),
    methods: {
      setGistData(event){
        let _this = this
        this.chartdata.labels=[]
        this.chartdata.datasets[0].data=[]

        this.gradient = this.$refs.canvas.getContext('2d').createLinearGradient(0, 0, 0, 450);
        this.gradient.addColorStop(0, '#9075da')
        this.gradient.addColorStop(1, '#727cf5');

        var gistData = event;

        for (var key in gistData) {
          this.chartdata.labels.push(key)
          this.chartdata.datasets[0].data.push(gistData[key])
        }

        this.chartdata.datasets[0]['backgroundColor']=this.gradient

        this.options['onClick'] =  function(evt,item) {
          if(item[0] && item[0]["_index"]){
            let index = item[0]["_index"];
            let name = item[0]["_chart"].data.labels[index];
            let value = item[0]["_chart"].data.datasets[0].data[index];

            let clicked = {
              'index' : index,
              'name' : name,
              'value' : value
            }
            _this.cellClickEvent(clicked)
          }
        }
        this.renderChart(this.chartdata, this.options)
      },
      cellClickEvent(element){
        console.log(element)
      }
    },
    watch:{
      chartObject(newValue) {
        this.renderChart({},{})
        this.setGistData(newValue)
      }
    },
    mounted () {
      this.setGistData(this.chartObject)

    },
  }
</script>
