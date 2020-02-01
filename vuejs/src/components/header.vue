<template>
  <div class="header">
    <div class="row">
      <v-img
        alt="Logo"
        class="shrink mr-2"
        contain
        transition="scale-transition"
        width="257"
        src="../assets/images/logo-dashboard.png"
      />
      <div class="side">
        <img 
          v-if='userData'
          alt="user"
          :src='userData.photo_url'
          class="round">
        <div
          v-if='userData'
          class="menu">
          <div class="username" v-on:click='showModal'>
            <p class="name">{{userData.first_name}} {{userData.last_name}}</p> 
            <p class="role">{{userData.role}}</p>
          </div>
          <div class="submenu" >
            <p>Login</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
      name: 'HeaderComponent',
      data: () => ({
          isShowDropdown: false,
          userData:null,
      }),
      methods: {
          showModal() {
              this.isShowDropdown = true
          },
          getUserData(){
            var userD = this.$store.state.user
            this.userData = userD
          },
      },
      created: function(){
        this.getUserData();
      },
  }
</script>

<style scoped lang="less">
  .header {
    position: relative;
    width: 100%;
    left: 0;
    right: 0;
    justify-content: space-between;
    display: block;
    align-items: center;
    background: #FFFFFF;
    box-shadow: 0px 4px 15px rgba(189, 196, 224, 0.192);
    min-width: 700px;
    margin-bottom: 60px;

    .logo {
      position: relative;
      right: 17px;
    }
    .row {
      padding-right: 30px;
      padding-left: 30px;
      height: 70px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .side {
      display: flex;
      align-items: center;
      position: relative;
      border: solid 1px #f1f3fa;
      background-color: #fafbfd;
      height:100%;
      padding: 0 18px;
    }

    .menu{
      position: relative;
      .submenu{
        display: none;
        position: absolute;
        top: 20px;
        left: 0;
        background-color: #F7F8FC;
        width: 128px;
        border: 1px solid #9092A4;
        padding:15px 0;
        border-radius: 7px;
        z-index: 10;
        p{
          cursor:pointer;
          &:not(:last-child){
            margin-bottom: 10px;
          }
          &:hover{
            background-color: #F0F1F7
          }
        }
      }
      &:hover .submenu{
        display: none;
      }
      .username {
        position: relative;
        font-family: Poppins;
        font-style: normal;
        font-weight: 600;
        font-size: 14px;
        line-height: 25px;
        white-space: nowrap;
        color: #220033;
        right: -2px;
        cursor: pointer;
        p{
          margin-bottom:0
        }
        .name{
          font-family: Nunito;
          font-size: 14.4px;
          font-weight: 600;
          font-stretch: normal;
          font-style: normal;
          line-height: 1.39;
          color: #98a6ad;

        }
        .role{
          font-family: Nunito;
          font-size: 12px;
          font-weight: normal;
          font-stretch: normal;
          font-style: normal;
          line-height: 1.33;
          color: #98a6ad;
        }
      }
    }
    .round {
      border-radius: 50%;
      margin-right: 10px;
      width: 38px;
    }

  }
</style>
