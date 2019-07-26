<template>
  <div class="select-col-cont" @mouseleave="mouseLeave" @mouseover="mouseOver">
    <div v-for="(colElem, index) in colMaxCount"
         class="select-col-elem"
         :class="[
          {'select-col-elem_hover':(index * 12 < hoverX)},
          {'select-col-elem_active':(index < selectedCols)},
         ]"
         :data-index="colElem"
         @click.stop.self="colClick">
    </div>
  </div>
</template>

<script>

export default {
  name: 'SelectCols',
  props: {
    colMaxCount: {
      type: Number,
      default: 4,
    },
    colsId: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      hoverX: 0,
    };
  },
  methods: {
    mouseOver(e) {
      this.hoverX = e.layerX;
    },
    mouseLeave() {
      this.hoverX = 0;
    },
    colClick(e) {
      const colIndex = parseInt(e.target.dataset.index);
      const chartID = this.colsId;
      console.log({ id: chartID, value: colIndex });
      this.$store.dispatch('settings/changeChartSize', { id: chartID, value: colIndex });
    },
  },
  computed: {
    selectedCols() {
      return this.$store.getters.chartSizes[this.colsId];
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .select-col-cont {
    display: flex;
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    padding: 1px;
    z-index: 2;
  }
  .select-col-elem {
    width: 10px;
    height: 10px;
    background-color: #e4e4e4;
    margin-left: 2px;
    cursor: pointer;
  }
  .select-col-elem_hover {
    background-color: #DD4A68 !important;
  }
  .select-col-elem_active {
    background-color: #59c76d;
  }
</style>
