import axios from 'axios'
// import store from '../store'

export default {
  name: 'HttpService',
  props: {
  },
  data: () => ({
  }),
  methods: {
    post (url, data) {
      // const setHeaders = store.state.headers
      // const setUrl = store.state.basicUrl + url
      const setUrl = localStorage.serve_host + url;
      return axios.post(
        setUrl,
        data,
        // url,
        // setHeaders
      )
    },
    get (url) {
      // const setHeaders = store.state.headers
      // const setUrl = store.state.basicUrl + url
      const setUrl = localStorage.serve_host + url;

      return axios.get(
        setUrl,
        // url,
        // setHeaders
      )
    },
    delete (url,data) {
      // const setHeaders = store.state.headers;
      const setUrl = localStorage.serve_host + url;
      return axios.delete(
        setUrl,
        data
        // setHeaders
      )
    },
    // postFiles (url, data) {
    //   var setHeaders = store.state.headers
    //   setHeaders['headers']['Content-Type'] = 'multipart/form-data'
    //   const setUrl = store.state.basicUrl + url

    //   return axios.post(
    //     setUrl,
    //     data,
    //     setHeaders
    //   )
    // },
    // put (url, data) {
    //   // const setHeaders = store.state.headers
    //   // const setUrl = store.state.basicUrl + url
    //   return axios.put(
    //     url,
    //     data,
    //     // setHeaders
    //   )
    // },
  
  }
}

// import axios from 'axios'
// import store from '../store'

// export default {
//   name: 'HttpService',
//   props: {
//   },
//   data: () => ({
//   }),
//   methods: {
//     post (url, data) {
//       const setHeaders = store.state.headers
//       const setUrl = store.state.basicUrl + url

//       return axios.post(
//         setUrl,
//         data,
//         setHeaders
//       )
//     },

//     postFiles (url, data) {
//       var setHeaders = store.state.headers
//       setHeaders['headers']['Content-Type'] = 'multipart/form-data'
//       const setUrl = store.state.basicUrl + url

//       return axios.post(
//         setUrl,
//         data,
//         setHeaders
//       )
//     },

//     get (url) {
//       const setHeaders = store.state.headers
//       const setUrl = store.state.basicUrl + url

//       return axios.get(
//         setUrl,
//         setHeaders
//       )
//     },

//     put (url, data) {
//       const setHeaders = store.state.headers
//       const setUrl = store.state.basicUrl + url

//       return axios.put(
//         setUrl,
//         data,
//         setHeaders
//       )
//     },

//     delete (url) {
//       const setHeaders = store.state.headers
//       const setUrl = store.state.basicUrl + url

//       return axios.delete(
//         setUrl,
//         setHeaders
//       )
//     },
//   }
// }
