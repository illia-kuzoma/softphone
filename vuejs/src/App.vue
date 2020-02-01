<template>
  <v-app id='app'>
      <router-view/>
  </v-app>
</template>

<script>
  export default {
    name: 'App',
    components: {
    },
    data: () => ({
    }),
  };
</script>

<style lang="less">
  @import "assets/less/main";
  #app{
    max-width: 1920px;
    min-width: 990px;
    background-color: #fafbfe;
    margin-right: auto;
    margin-left: auto;
  }
</style>
