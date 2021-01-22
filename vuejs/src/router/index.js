import Vue from 'vue'
import VueRouter from 'vue-router'
// import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import MissedCalls from '../components/MissedCalls.vue'
import AgentStatusTimeTracking from '../components/AgentStatusTimeTracking.vue'
// import Another from '../components/Another.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login
  },
  {
    path: '/dashboard/:id',
    name: 'Dashboard',
    component: Dashboard,
    children: [
        {
          path: "/dashboard/missed-calls",
          component: MissedCalls
        },
        {
          path: "/dashboard/agent-status",
          component: AgentStatusTimeTracking
        },
        // {
        //   path: "/dashboard/another",
        //   component: Another
        // }
    ]
  },
  // {
  //   path: '/dashboard/MissedCalls',
  //   name: 'MissedCalls',
  //   component: MissedCalls
  // },
  // {
  //   path: '/dashboard/AgentStatusTimeTracking',
  //   name: 'AgentStatusTimeTracking',
  //   component: AgentStatusTimeTracking
  // },



  // {
  //   path: '/about',
  //   name: 'about',
  //   // route level code-splitting
  //   // this generates a separate chunk (about.[hash].js) for this route
  //   // which is lazy-loaded when the route is visited.
  //   component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  // }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router

