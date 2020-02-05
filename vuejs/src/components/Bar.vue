<script>
import { Bar } from "vue-chartjs";

export default {
  extends: Bar,
  props: ["chartObject"],
  data: () => ({
    gradient: null,
    clickedGradient: null,
    type: "horizontalBar",
    chartdata: {
      labels: [],
      datasets: [
        {
          label: "MISSED CALLS",
          data: [],
          backgroundColor: [],
        }
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      // hover: {mode: null},
      legend: {
        display: false
      },
      animation: {
        duration: 0
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
              precision: 0
            }
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
    }
  }),
  methods: {
    setGistData(event) {
      let self = this;
      let gistData = event;
      let colorMain = this.$refs.canvas
       .getContext("2d")
       .createLinearGradient(0, 0, 0, 450);
      colorMain.addColorStop(0, "#727cf5");
     colorMain.addColorStop(1, "#9075da");

     let colorSelected = this.$refs.canvas
       .getContext("2d")
       .createLinearGradient(0, 0, 0, 450);
      colorSelected.addColorStop(0, "#6421A7");
     colorSelected.addColorStop(1, "#6421A7");

      this.backgroundColor = [];
      this.chartdata.labels = [];
      this.chartdata.datasets[0].data = [];

      for (var key in gistData) {
        this.chartdata.labels.push(key);
        this.chartdata.datasets[0].data.push(gistData[key]);
        this.chartdata.datasets[0]["backgroundColor"].push(colorMain);
      }

      this.options["onClick"] = function(evt, elems) {
        if (elems[0]) {
          let index = elems[0]["_index"];
          let name = elems[0]["_chart"].data.labels[index];
          let value = elems[0]["_chart"].data.datasets[0].data[index];

          let clicked = {
            index: index,
            name: name,
            value: value
          };

          self.cellClickEvent(clicked);
          var backgroundColors = self.chartdata.datasets[0]["backgroundColor"];

          if (backgroundColors[index] === colorSelected) {
            backgroundColors[index] = colorMain;
          } else {
            for (var i = 0; i < backgroundColors.length; i++) {
              backgroundColors[i] = colorMain;
            }
            backgroundColors[index] = colorSelected;
          }
          // self.renderChart(self.chartdata, self.options);
        }
        self.renderChart(self.chartdata, self.options);
      };
      this.renderChart(this.chartdata, this.options);
    },
    cellClickEvent(element) {
      this.$emit("clicked", element);
    }
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
