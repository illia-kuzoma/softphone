<script>
import { Bar } from "vue-chartjs";

export default {
  extends: Bar,
  props: ["chartObject"],
  data: () => ({
    gradient: null,
    type: "horizontalBar",
    chartdata: {
      labels: [],
      datasets: [
        {
          label: "MISSED CALLS",
          data: []
        }
      ],
      chartObject: null
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: false
      },
      scales: {
        yAxes: [
          {
            display: true,
            gridLines: {
              display: false
            },
            ticks: {
              beginAtZero: true,
              min: 0,
              precision: 0,
            },
          }
        ],
        xAxes: [
          {
            gridLines: {
              display: false
            }
          }
        ]
      }
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
    setGistData(event) {
      let self = this;
      this.chartdata.labels = [];
      this.chartdata.datasets[0].data = [];

      this.gradient = this.$refs.canvas
        .getContext("2d")
        .createLinearGradient(0, 0, 0, 450);
      this.gradient.addColorStop(0, "#727cf5");
      this.gradient.addColorStop(1, "#9075da");

      var gistData = event;

      for (var key in gistData) {
        this.chartdata.labels.push(key);
        this.chartdata.datasets[0].data.push(gistData[key]);
      }

      this.chartdata.datasets[0]["backgroundColor"] = this.gradient;

      this.options["onClick"] = function(evt, item) {

        if (item[0] && item[0]["_index"]) {
          let index = item[0]["_index"];
          let name = item[0]["_chart"].data.labels[index];
          let value = item[0]["_chart"].data.datasets[0].data[index];

          let clicked = {
            index: index,
            name: name,
            value: value
          };
          self.cellClickEvent(clicked);
        }
      };
      this.renderChart(this.chartdata, this.options);
    },
    cellClickEvent(element) {
      this.$emit("clicked", element);
    },

  },
  watch: {
    chartObject(newValue) {
      this.renderChart({}, {});
      this.setGistData(newValue);
    }
  },
  mounted() {
    this.setGistData(this.chartObject);
  }
};
</script>