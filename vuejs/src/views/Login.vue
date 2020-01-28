<template>
  <v-container
    id="login"
    fluid
    fill-height
    >

    <v-content>
      <v-container
        fluid
        fill-height
        >
        <v-layout
          align-center
          justify-center
          >
          <v-flex
            class='login-form'
            >
            <v-card class="elevation-12">
              <v-toolbar
                flat
                class='login-header'
                >

               <v-img
                  alt="Logo"
                  class="shrink login-logo"
                  contain
                  transition="scale-transition"
                  width="163"
                  src="../assets/logo-wellness.svg"
                />

              </v-toolbar>
              <v-card-text>
                
                <v-form class='form'>
                  <p class='placeholder'>EMAIL</p>

                  <input
                    type="text"
                    id="email"
                    v-model="email"
                    required
                    v-bind:class="{inputWarning: isValid}"
                    name="email">

                  <div style='display:flex; justify-content:space-between'>
                    <p class='placeholder'>PASSWORD</p>
                    <p
                      class="placeholder forgot" 
                      @click="forgotFunc">
                      Forgot Password?
                    </p>
                  </div>

                  <input
                    id="password"
                    type="password"
                    v-model="password"
                    required
                    v-bind:class="{inputWarning: isValid}"
                    name="password">
                </v-form>

              </v-card-text>

              <v-card-actions>
                <v-btn
                  class="login-button"
                  @click="validate"
                  >Log In
                </v-btn>
                  <!-- :disabled='!isValid' -->
                  <!-- v-bind:class="{buttonDisabled: !isValid}" -->
                  <!-- [disabled]='!isValid' -->
                  <!-- v-bind:class="{disabled: !isValid}" -->
                <!-- <v-btn class="login-button" @click="validate" [disabled]='isValid'>Log In</v-btn> -->
              </v-card-actions>

            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-content>

  </v-container>
</template>

<script>
  import router from '../router'
  import HttpService from '@/services/HttpService'
  // import store from '../store'

  export default {
    name: 'Login',

    data: () => ({
      email:'',
      password:'',
      isValid:false,

    }),
    methods: {
      validate(){

        if(this.email =='' || this.password =='' ){
          this.isValid = true
        }

        if(this.email !=='' && this.password !=='' ){
          this.login()
        }
      },
      login(){
        var self = this
        HttpService.methods.post('http://callcentr.wellnessliving.com/auth',{
          'email':this.email,
          'password':this.password,
        })
          .then(function (response) {
            // console.log(response.status)
            // console.log(result)
            if(response.status === 200){
              // console.log('do something with user data',response.data.user)
              self.$store.state.user = response.data.user
              // this.store.state.user = response.data.user;
              // console.log('login get store',self.$store.state.user)
              router.push('/dashboard')
            }
          })
          .catch(function (error) {
            console.log(error)
            // this.isValid = false
          })
      },

      forgotFunc(){
        console.log('forgot password')
      }
    },
    created: function(){
      // console.log(store)
    },
  };
</script>

<style scoped lang="less">
  #login{
    background-color: #e5f0f6;
    .login-form{
      max-width: 322px;
    }
    .elevation-12{
      padding: 20px 20px 20px 20px;
      box-shadow: none !important;
    }
    .login-header{
      display: flex !important;
      justify-content: center;
    }
    .v-application p,
    .placeholder{
      font-family: NunitoSans;
      font-size: 12px;
      font-weight: bold;
      font-stretch: normal;
      font-style: normal;
      line-height: normal;
      letter-spacing: 1px;
      color: #7f7f7f;
      margin-bottom: 2px;
    }
    .login-button{
      background-color: #2a7ab0;
      width: 100%;
      font-family: NunitoSans;
      font-size: 14px;
      color: #ffffff;
      text-transform:none ;
      border-radius: 0;
      height: 44px;
    }

    v-card-text{
      padding: 16px 13px;
    }
    .v-card__text{
      padding: 16px 12px 12px;
    }
    .v-card__actions{
      padding: 8px 16px;

    }
    .form{
      margin: 6px 0 7px;
      #email{
        margin-bottom: 26px
      }
      input{
        border: 1px solid #e8ecee;
        width: 100%;
        padding: 10px;
      }
      .inputWarning{
        border-color: red;
      }
      .buttonDisabled{
        pointer-events: none;
        cursor: not-allowed;
        opacity: 0.65;
        filter: alpha(opacity=65);
        -webkit-box-shadow: none;
        box-shadow: none;
      }
      .forgot{
        font-weight:normal;
        cursor: pointer;
        letter-spacing: normal
      }
    }
  }
</style>
