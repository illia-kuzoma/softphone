(function(t){function e(e){for(var s,o,r=e[0],c=e[1],l=e[2],u=0,h=[];u<r.length;u++)o=r[u],Object.prototype.hasOwnProperty.call(n,o)&&n[o]&&h.push(n[o][0]),n[o]=0;for(s in c)Object.prototype.hasOwnProperty.call(c,s)&&(t[s]=c[s]);d&&d(e);while(h.length)h.shift()();return i.push.apply(i,l||[]),a()}function a(){for(var t,e=0;e<i.length;e++){for(var a=i[e],s=!0,r=1;r<a.length;r++){var c=a[r];0!==n[c]&&(s=!1)}s&&(i.splice(e--,1),t=o(o.s=a[0]))}return t}var s={},n={app:0},i=[];function o(e){if(s[e])return s[e].exports;var a=s[e]={i:e,l:!1,exports:{}};return t[e].call(a.exports,a,a.exports,o),a.l=!0,a.exports}o.m=t,o.c=s,o.d=function(t,e,a){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:a})},o.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var a=Object.create(null);if(o.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var s in t)o.d(a,s,function(e){return t[e]}.bind(null,s));return a},o.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="/";var r=window["webpackJsonp"]=window["webpackJsonp"]||[],c=r.push.bind(r);r.push=e,r=r.slice();for(var l=0;l<r.length;l++)e(r[l]);var d=c;i.push([0,"chunk-vendors"]),a()})({0:function(t,e,a){t.exports=a("56d7")},"3a34":function(t,e,a){},4678:function(t,e,a){var s={"./af":"2bfb","./af.js":"2bfb","./ar":"8e73","./ar-dz":"a356","./ar-dz.js":"a356","./ar-kw":"423e","./ar-kw.js":"423e","./ar-ly":"1cfd","./ar-ly.js":"1cfd","./ar-ma":"0a84","./ar-ma.js":"0a84","./ar-sa":"8230","./ar-sa.js":"8230","./ar-tn":"6d83","./ar-tn.js":"6d83","./ar.js":"8e73","./az":"485c","./az.js":"485c","./be":"1fc1","./be.js":"1fc1","./bg":"84aa","./bg.js":"84aa","./bm":"a7fa","./bm.js":"a7fa","./bn":"9043","./bn.js":"9043","./bo":"d26a","./bo.js":"d26a","./br":"6887","./br.js":"6887","./bs":"2554","./bs.js":"2554","./ca":"d716","./ca.js":"d716","./cs":"3c0d","./cs.js":"3c0d","./cv":"03ec","./cv.js":"03ec","./cy":"9797","./cy.js":"9797","./da":"0f14","./da.js":"0f14","./de":"b469","./de-at":"b3eb","./de-at.js":"b3eb","./de-ch":"bb71","./de-ch.js":"bb71","./de.js":"b469","./dv":"598a","./dv.js":"598a","./el":"8d47","./el.js":"8d47","./en-SG":"cdab","./en-SG.js":"cdab","./en-au":"0e6b","./en-au.js":"0e6b","./en-ca":"3886","./en-ca.js":"3886","./en-gb":"39a6","./en-gb.js":"39a6","./en-ie":"e1d3","./en-ie.js":"e1d3","./en-il":"7333","./en-il.js":"7333","./en-nz":"6f50","./en-nz.js":"6f50","./eo":"65db","./eo.js":"65db","./es":"898b","./es-do":"0a3c","./es-do.js":"0a3c","./es-us":"55c9","./es-us.js":"55c9","./es.js":"898b","./et":"ec18","./et.js":"ec18","./eu":"0ff2","./eu.js":"0ff2","./fa":"8df4","./fa.js":"8df4","./fi":"81e9","./fi.js":"81e9","./fo":"0721","./fo.js":"0721","./fr":"9f26","./fr-ca":"d9f8","./fr-ca.js":"d9f8","./fr-ch":"0e49","./fr-ch.js":"0e49","./fr.js":"9f26","./fy":"7118","./fy.js":"7118","./ga":"5120","./ga.js":"5120","./gd":"f6b4","./gd.js":"f6b4","./gl":"8840","./gl.js":"8840","./gom-latn":"0caa","./gom-latn.js":"0caa","./gu":"e0c5","./gu.js":"e0c5","./he":"c7aa","./he.js":"c7aa","./hi":"dc4d","./hi.js":"dc4d","./hr":"4ba9","./hr.js":"4ba9","./hu":"5b14","./hu.js":"5b14","./hy-am":"d6b6","./hy-am.js":"d6b6","./id":"5038","./id.js":"5038","./is":"0558","./is.js":"0558","./it":"6e98","./it-ch":"6f12","./it-ch.js":"6f12","./it.js":"6e98","./ja":"079e","./ja.js":"079e","./jv":"b540","./jv.js":"b540","./ka":"201b","./ka.js":"201b","./kk":"6d79","./kk.js":"6d79","./km":"e81d","./km.js":"e81d","./kn":"3e92","./kn.js":"3e92","./ko":"22f8","./ko.js":"22f8","./ku":"2421","./ku.js":"2421","./ky":"9609","./ky.js":"9609","./lb":"440c","./lb.js":"440c","./lo":"b29d","./lo.js":"b29d","./lt":"26f9","./lt.js":"26f9","./lv":"b97c","./lv.js":"b97c","./me":"293c","./me.js":"293c","./mi":"688b","./mi.js":"688b","./mk":"6909","./mk.js":"6909","./ml":"02fb","./ml.js":"02fb","./mn":"958b","./mn.js":"958b","./mr":"39bd","./mr.js":"39bd","./ms":"ebe4","./ms-my":"6403","./ms-my.js":"6403","./ms.js":"ebe4","./mt":"1b45","./mt.js":"1b45","./my":"8689","./my.js":"8689","./nb":"6ce3","./nb.js":"6ce3","./ne":"3a39","./ne.js":"3a39","./nl":"facd","./nl-be":"db29","./nl-be.js":"db29","./nl.js":"facd","./nn":"b84c","./nn.js":"b84c","./pa-in":"f3ff","./pa-in.js":"f3ff","./pl":"8d57","./pl.js":"8d57","./pt":"f260","./pt-br":"d2d4","./pt-br.js":"d2d4","./pt.js":"f260","./ro":"972c","./ro.js":"972c","./ru":"957c","./ru.js":"957c","./sd":"6784","./sd.js":"6784","./se":"ffff","./se.js":"ffff","./si":"eda5","./si.js":"eda5","./sk":"7be6","./sk.js":"7be6","./sl":"8155","./sl.js":"8155","./sq":"c8f3","./sq.js":"c8f3","./sr":"cf1e","./sr-cyrl":"13e9","./sr-cyrl.js":"13e9","./sr.js":"cf1e","./ss":"52bd","./ss.js":"52bd","./sv":"5fbd","./sv.js":"5fbd","./sw":"74dc","./sw.js":"74dc","./ta":"3de5","./ta.js":"3de5","./te":"5cbb","./te.js":"5cbb","./tet":"576c","./tet.js":"576c","./tg":"3b1b","./tg.js":"3b1b","./th":"10e8","./th.js":"10e8","./tl-ph":"0f38","./tl-ph.js":"0f38","./tlh":"cf75","./tlh.js":"cf75","./tr":"0e81","./tr.js":"0e81","./tzl":"cf51","./tzl.js":"cf51","./tzm":"c109","./tzm-latn":"b53d","./tzm-latn.js":"b53d","./tzm.js":"c109","./ug-cn":"6117","./ug-cn.js":"6117","./uk":"ada2","./uk.js":"ada2","./ur":"5294","./ur.js":"5294","./uz":"2e8c","./uz-latn":"010e","./uz-latn.js":"010e","./uz.js":"2e8c","./vi":"2921","./vi.js":"2921","./x-pseudo":"fd7e","./x-pseudo.js":"fd7e","./yo":"7f33","./yo.js":"7f33","./zh-cn":"5c3a","./zh-cn.js":"5c3a","./zh-hk":"49ab","./zh-hk.js":"49ab","./zh-tw":"90ea","./zh-tw.js":"90ea"};function n(t){var e=i(t);return a(e)}function i(t){if(!a.o(s,t)){var e=new Error("Cannot find module '"+t+"'");throw e.code="MODULE_NOT_FOUND",e}return s[t]}n.keys=function(){return Object.keys(s)},n.resolve=i,t.exports=n,n.id="4678"},"56d7":function(t,e,a){"use strict";a.r(e);a("e260"),a("e6cf"),a("cca6"),a("a79d");var s=a("2b0e"),n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-app",{attrs:{id:"app"}},[a("router-view")],1)},i=[],o={name:"App",components:{},data:function(){return{}}},r=o,c=(a("7c55"),a("2877")),l=a("6544"),d=a.n(l),u=a("7496"),h=Object(c["a"])(r,n,i,!1,null,null,null),f=h.exports;d()(h,{VApp:u["a"]});var p=a("8c4f"),b=function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("v-container",{attrs:{id:"login",fluid:"","fill-height":""}},[s("v-content",[s("v-container",{attrs:{fluid:"","fill-height":""}},[s("v-layout",{attrs:{"align-center":"","justify-center":""}},[s("v-flex",{staticClass:"login-form"},[s("v-card",{staticClass:"elevation-12"},[s("v-toolbar",{staticClass:"login-header",attrs:{flat:""}},[s("v-img",{staticClass:"shrink login-logo",attrs:{alt:"Logo",contain:"",transition:"scale-transition",width:"163",src:a("b463")}})],1),s("v-card-text",[s("v-form",{staticClass:"form"},[s("p",{staticClass:"placeholder"},[t._v("EMAIL")]),s("input",{directives:[{name:"model",rawName:"v-model",value:t.email,expression:"email"}],class:{inputWarning:t.isValid},attrs:{type:"text",id:"email",required:"",name:"email"},domProps:{value:t.email},on:{input:function(e){e.target.composing||(t.email=e.target.value)}}}),s("div",{staticStyle:{display:"flex","justify-content":"space-between"}},[s("p",{staticClass:"placeholder"},[t._v("PASSWORD")]),s("p",{staticClass:"placeholder forgot",on:{click:t.forgotFunc}},[t._v(" Forgot Password? ")])]),s("input",{directives:[{name:"model",rawName:"v-model",value:t.password,expression:"password"}],class:{inputWarning:t.isValid},attrs:{id:"password",type:"password",required:"",name:"password"},domProps:{value:t.password},on:{input:function(e){e.target.composing||(t.password=e.target.value)}}})])],1),s("v-card-actions",[s("v-btn",{staticClass:"login-button",on:{click:t.validate}},[t._v("Log In ")])],1)],1)],1)],1)],1)],1)],1)},g=[],v=a("bc3a"),m=a.n(v),j={name:"HttpService",props:{},data:function(){return{}},methods:{post:function(t,e){return m.a.post(t,e)},get:function(t){return m.a.get(t)}}},_={name:"Login",data:function(){return{email:"",password:"",isValid:!1}},methods:{validate:function(){""!=this.email&&""!=this.password||(this.isValid=!0),""!==this.email&&""!==this.password&&this.login()},login:function(){var t=this;j.methods.post("http://callcentr.wellnessliving.com/auth",{email:this.email,password:this.password}).then((function(e){e.data.user&&(t.$store.state.user=e.data.user,bt.push("/dashboard")),!0===e.data.error&&(t.isValid=!0,alert(e.data.message))})).catch((function(t){console.log(t)}))},forgotFunc:function(){console.log("forgot password")}},created:function(){}},D=_,y=(a("b6a6"),a("8336")),C=a("b0af"),k=a("99d9"),w=a("a523"),x=a("a75b"),S=a("0e8f"),P=a("4bd4"),O=a("adda"),A=a("a722"),T=a("71d9"),z=Object(c["a"])(D,b,g,!1,null,"55f3e5dc",null),V=z.exports;d()(z,{VBtn:y["a"],VCard:C["a"],VCardActions:k["a"],VCardText:k["b"],VContainer:w["a"],VContent:x["a"],VFlex:S["a"],VForm:P["a"],VImg:O["a"],VLayout:A["a"],VToolbar:T["a"]});var L=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{attrs:{id:"dashboard"}},[a("HeaderComponent"),a("div",{staticClass:"dashboard"},[t._m(0),a("div",{staticClass:"controls-container"},[a("div",{staticClass:"date-container"},[a("button",{staticClass:"button button-li date-item color-grey",on:{click:function(e){t.setDate("today"),t.setActive(e)}}},[t._v("Today ")]),a("date-picker",{staticClass:"date-item",attrs:{type:"date",valueType:"YYYY-MM-DD",format:"DD/MM/YYYY"},model:{value:t.selectedDate,callback:function(e){t.selectedDate=e},expression:"selectedDate"}}),a("button",{staticClass:"button button-li date-item color-grey",on:{click:function(e){t.setDate("day"),t.setActive(e)}}},[t._v("Day ")]),a("button",{staticClass:"button button-li date-item color-grey color-dark",on:{click:function(e){t.setDate("week"),t.setActive(e)}}},[t._v("Week ")]),a("button",{staticClass:"button button-li date-item color-grey",on:{click:function(e){t.setDate("month"),t.setActive(e)}}},[t._v("Month ")]),a("button",{staticClass:"button button-li date-item color-grey",on:{click:function(e){t.setDate("year"),t.setActive(e)}}},[t._v("Year ")])],1),a("div",{staticClass:"search-container"},[a("input",{directives:[{name:"model",rawName:"v-model",value:t.searchText,expression:"searchText"}],attrs:{type:"text",id:"search",placeholder:"Search...",name:"search"},domProps:{value:t.searchText},on:{input:function(e){e.target.composing||(t.searchText=e.target.value)}}}),a("button",{staticClass:"button color-violet",attrs:{id:"search-button"},on:{click:function(e){t.search(t.searchText)}}},[t._v("Search ")])])]),a("div",{staticClass:"controls-container"},[a("div",[a("label",{staticClass:"typo__label"},[t._v("Select agents you need:")]),a("multiselect",{attrs:{options:t.multiple_options,multiple:!0,"close-on-select":!1,"clear-on-select":!1,"preserve-search":!0,placeholder:"Pick some",label:"name","track-by":"value","preselect-first":!0},on:{close:t.setUsers},scopedSlots:t._u([{key:"selection",fn:function(e){var s=e.values,n=(e.search,e.isOpen);return[s.length&&!n?a("span",{staticClass:"multiselect__single"},[t._v(t._s(s.length)+" options selected")]):t._e()]}}]),model:{value:t.multiple_selected_value,callback:function(e){t.multiple_selected_value=e},expression:"multiple_selected_value"}})],1)]),0!==Object.keys(t.chartData).length?a("div",{staticClass:"chart-container"},[a("h2",[t._v("MISSED CALLS")]),t.chartData?a("line-chart",{staticClass:"chart",attrs:{id:"chartId",chartObject:t.chartData,"is-resizable":!0,"use-css-transforms":!0},on:{clicked:t.getAgentFromChart,close:t.ffFff}}):t._e()],1):t._e(),0===Object.keys(t.chartData).length?a("div",{staticClass:"table-container"},[a("h1",[t._v("No Chart Data For This Period")])]):t._e(),t.tableCallsData.length>0?a("div",{staticClass:"table-container"},[a("v-data-table",{staticClass:"v-data-table elevation-1",attrs:{headers:t.tableCallsHeaders,items:t.tableCallsData,"items-per-page":20,page:t.tablePage,options:t.options,"hide-default-footer":!0,"fixed-header":""},on:{"update:page":[function(e){t.tablePage=e},t.updatePage],"update:options":function(e){t.options=e},"update:sort-desc":t.updateSortDesc},scopedSlots:t._u([{key:"item.user_data",fn:function(e){var s=e.item;return[a("div",{staticClass:"user"},[a("img",{attrs:{src:s.user_data.photo_url,alt:""}}),a("p",[t._v(t._s(s.user_data.full_name))])])]}},{key:"item.business",fn:function(e){var s=e.item;return[a("div",{staticClass:"business"},[a("a",{staticClass:"url",attrs:{target:"_blank",href:s.business.business_link},on:{click:function(e){return t.goOutTo(s.business.business_link)}}},[t._v(t._s(s.business.business_name)+" ")])])]}},{key:"item.contact",fn:function(e){var s=e.item;return[a("div",{staticClass:"user"},[a("a",{staticClass:"url",attrs:{target:"_blank",href:s.contact.contact_link},on:{click:function(e){return t.goOutTo(s.contact.contact_link)}}},[t._v(t._s(s.contact.contact_name)+" ")])])]}},{key:"item.time_create",fn:function(e){var s=e.item;return[a("div",{staticClass:"table-date-cell"},[t._v(t._s(t.getDate(s["time_create"])))])]}}],null,!1,631757644)}),t.tablePageCount>1?a("v-pagination",{staticClass:"table-pagination",attrs:{length:t.tablePageCount,"next-icon":t.nextIcon,"prev-icon":t.prevIcon},on:{input:t.changePage},model:{value:t.tablePage,callback:function(e){t.tablePage=e},expression:"tablePage"}}):t._e()],1):t._e(),0==t.tableCallsData.length?a("div",{staticClass:"table-container"},[a("h1",[t._v("No Table Data For This Period")])]):t._e()]),a("Loader")],1)},M=[function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"header-container"},[a("h1",[t._v("Missed Calls")])])}],B=function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"header"},[s("div",{staticClass:"row"},[s("v-img",{staticClass:"shrink mr-2",attrs:{alt:"Logo",contain:"",transition:"scale-transition",width:"257",src:a("6d80")}}),s("div",{staticClass:"side"},[t.userData?s("img",{staticClass:"round",attrs:{alt:"user",src:t.userData.photo_url}}):t._e(),t.userData?s("div",{staticClass:"menu"},[s("div",{staticClass:"username",on:{click:t.showModal}},[s("p",{staticClass:"name"},[t._v(t._s(t.userData.first_name)+" "+t._s(t.userData.last_name))]),s("p",{staticClass:"role"},[t._v(t._s(t.userData.role))])]),t._m(0)]):t._e()])],1)])},I=[function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"submenu"},[a("p",[t._v("Login")])])}],$={name:"HeaderComponent",data:function(){return{isShowDropdown:!1,userData:null}},methods:{showModal:function(){this.isShowDropdown=!0},getUserData:function(){var t=this.$store.state.user;this.userData=t}},created:function(){this.getUserData()}},E=$,F=(a("ffa5"),Object(c["a"])(E,B,I,!1,null,"1af67b79",null)),U=F.exports;d()(F,{VImg:O["a"]});var Y=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-dialog",{attrs:{"hide-overlay":"",persistent:"",width:"300"},model:{value:t.$store.state.loader,callback:function(e){t.$set(t.$store.state,"loader",e)},expression:"$store.state.loader"}},[a("v-card",{attrs:{color:"primary",dark:""}},[a("v-card-text",[t._v(" Please stand by "),a("v-progress-linear",{staticClass:"mb-0",attrs:{indeterminate:"",color:"white"}})],1)],1)],1)},G=[],N=a("169a"),H=a("8e36"),q={},W=Object(c["a"])(q,Y,G,!1,null,null,null),J=W.exports;d()(W,{VCard:C["a"],VCardText:k["b"],VDialog:N["a"],VProgressLinear:H["a"]});var R,Z,K=a("ec45"),Q=(a("411c"),a("1fca")),X={extends:Q["a"],props:["chartObject"],data:function(){return{gradient:null,clickedGradient:null,type:"horizontalBar",chartdata:{labels:[],datasets:[{label:"MISSED CALLS",data:[],backgroundColor:[]}]},options:{responsive:!0,maintainAspectRatio:!1,legend:{display:!1},animation:{duration:0},scales:{yAxes:[{display:!0,gridLines:{display:!1},ticks:{beginAtZero:!0,min:0,precision:0}}],xAxes:[{gridLines:{display:!1}}]}}}},methods:{setGistData:function(t){var e=this,a=t,s=this.$refs.canvas.getContext("2d").createLinearGradient(0,0,0,450);s.addColorStop(0,"#727cf5"),s.addColorStop(1,"#9075da");var n=this.$refs.canvas.getContext("2d").createLinearGradient(0,0,0,450);for(var i in n.addColorStop(0,"#6421A7"),n.addColorStop(1,"#6421A7"),this.backgroundColor=[],this.chartdata.labels=[],this.chartdata.datasets[0].data=[],a)this.chartdata.labels.push(i),this.chartdata.datasets[0].data.push(a[i]),this.chartdata.datasets[0]["backgroundColor"].push(s);this.options["onClick"]=function(t,a){if(a[0]){var i=a[0]["_index"],o=a[0]["_chart"].data.labels[i],r=a[0]["_chart"].data.datasets[0].data[i],c={index:i,name:o,value:r};e.cellClickEvent(c);var l=e.chartdata.datasets[0]["backgroundColor"];if(l[i]===n)l[i]=s;else{for(var d=0;d<l.length;d++)l[d]=s;l[i]=n}}e.renderChart(e.chartdata,e.options)},this.renderChart(this.chartdata,this.options)},cellClickEvent:function(t){this.$emit("clicked",t)}},watch:{chartObject:function(t){this.renderChart({},{}),this.setGistData(t)}},mounted:function(){this.setGistData(this.chartObject)}},tt=X,et=Object(c["a"])(tt,R,Z,!1,null,null,null),at=et.exports,st=a("c1df"),nt=a.n(st),it=a("8e5f"),ot=a.n(it),rt={name:"HelloWorld",components:{HeaderComponent:U,DatePicker:K["a"],LineChart:at,Multiselect:ot.a,Loader:J},data:function(){return{selectedDate:null,dateType:"date",chartData:[],tableCallsData:[],serverChartData:null,options:{},tableCallsHeaders:[{text:"Agent",value:"user_data"},{text:"Business Name",value:"business"},{text:"Contact",value:"contact",width:200},{text:"Priority",value:"priority",sortable:!1},{text:"Phone",value:"phone",sortable:!1,width:150},{text:"Created Time",value:"time_create",sortable:!1,width:120}],tablePage:null,tableSort:null,tablePageCount:null,tableItemsPerPage:null,searchText:null,selectedAgent:null,selectedAgentUid:null,multiple_value:null,multiple_options:[],multiple_selected_value:null,nextIcon:">",prevIcon:"<",s_agent_id:"",period:"week"}},methods:{getDate:function(t){var e=new Date(1e3*t),a=nt()(e).format("ll"),s=nt()(e).format("hh:mm:ss a");return s+" "+a},setUsers:function(){this.selectedAgentUid=null,this.setDate(this.period)},ffFff:function(){console.log("!!!!!!!!")},setDate:function(t){switch(this.period=t,this.searchText="",this.period){case"today":var e=new Date,a=e.getDate(),s=e.getMonth()+1,n=e.getFullYear();a<10&&(a="0"+a),s<10&&(s="0"+s),e=n+"-"+s+"-"+a,this.selectedDate=e,this.getDataByDate(this.selectedDate,"day"),this.period="day";break;case"day":this.getDataByDate(this.selectedDate,"day"),this.period="day";break;case"week":this.getDataByDate(this.selectedDate,"week"),this.period="week";break;case"month":this.getDataByDate(this.selectedDate,"month"),this.period="month";break;case"year":this.getDataByDate(this.selectedDate,"year"),this.period="year";break;default:this.period="week";break}},datePickerSetDefaultPeriod:function(t){var e=new Date,a=e.getDate(),s=e.getMonth()+1,n=e.getFullYear();a<10&&(a="0"+a),s<10&&(s="0"+s),e=n+"-"+s+"-"+a,this.selectedDate=e,console.log(t),this.period=t},setActive:function(t){document.querySelector(".color-dark").classList.remove("color-dark"),t.target.classList.add("color-dark")},search:function(){console.log(),this.getDataByOptions()},getAgentFromChart:function(t){return this.selectedAgentUid===this.serverChartData[t["index"]]["uid"]?(this.selectedAgent=null,this.selectedAgentUid=this.s_agent_id||null,void this.getDataByOptions()):t?(this.selectedAgent=this.serverChartData[t["index"]],this.selectedAgentUid=this.serverChartData[t["index"]]["uid"],void this.getDataByOptions()):void 0},changePage:function(t){this.options.page=t,this.getDataByOptions()},goOutTo:function(t){console.log("authrize and goto",t)},updatePage:function(){this.getDataByOptions()},updateSortDesc:function(){this.getDataByOptions()},getChartData:function(){var t=this;j.methods.get("http://softphone/report/missed").then((function(e){t.setChartData(e.data.diagrama),t.setTableData(e.data.calls),t.setMultiDropdown(e.data.agents),t.datePickerSetDefaultPeriod(t.period)})).catch((function(t){console.log(t)}))},getDataByOptions:function(){var t=this;t.generateSelectedAgentIdString();var e=this.selectedDate||"-",a=this.period||"-",s=this.selectedAgentUid||this.s_agent_id||"-",n=this.searchText||"-",i=this.options.page||"-",o=this.options.sortBy[0]||"-";"user_data"===this.options.sortBy[0]&&(o="first_name"),"business"===this.options.sortBy[0]&&(o="business_name"),"contact"===this.options.sortBy[0]&&(o="contact");var r=this.options.sortDesc[0]||"-";!1===this.options.sortDesc[0]&&(r="asc"),!0===this.options.sortDesc[0]&&(r="desc"),j.methods.get("http://softphone/report/missed/call/"+e+"/"+a+"/"+s+"/"+n+"/"+o+"/"+r+"/"+i).then((function(e){var a=e.data.calls;t.setTableData(a)})).catch((function(t){console.log(t)}))},getDataByDate:function(t,e){var a=this;a.generateSelectedAgentIdString();var s="";""!==a.s_agent_id&&(s="/"+a.s_agent_id),j.methods.get("http://softphone/report/missed/call/"+t+"/"+e+s).then((function(t){var e=t.data.calls;a.setTableData(e),a.setChartData(t.data.diagrama)})).catch((function(t){console.log(t)}))},getTableData:function(){var t=this;j.methods.get("http://softphone/report/missed/call").then((function(e){var a=e.data.calls;t.setTableData(a)})).catch((function(t){console.log(t)}))},setChartData:function(t){var e={};this.serverChartData=t;for(var a=0;a<t.length;a++){var s=t[a].full_name,n=t[a].calls_count;e[s]=n}this.chartData=e},setTableData:function(t){this.tableCallsData=t.data,this.tablePage=parseInt(t.page),this.tablePageCount=t.pages_count},setMultiDropdown:function(t){console.log(t),this.multiple_options=t},generateSelectedAgentIdString:function(){console.log("generateSelectedAgentIdString");var t="";if(null!==this.multiple_selected_value){var e=this.multiple_selected_value,a=e.length;if(console.log(a),a)for(var s=0;s<a;s++)t+=e[s].value,s+1!==a&&(t+=",")}this.s_agent_id=t}},created:function(){this.getChartData()},mounted:function(){}},ct=rt,lt=(a("60bc"),a("6258"),a("8fea")),dt=a("891e"),ut=Object(c["a"])(ct,L,M,!1,null,"1808babd",null),ht=ut.exports;d()(ut,{VDataTable:lt["a"],VPagination:dt["a"]}),s["a"].use(p["a"]);var ft=[{path:"/",name:"Login",component:V},{path:"/dashboard",name:"Dashboard",component:ht}],pt=new p["a"]({mode:"history",base:"/",routes:ft}),bt=pt,gt=a("2f62");s["a"].use(gt["a"]);var vt=new gt["a"].Store({state:{},mutations:{},actions:{}}),mt=a("f309");s["a"].use(mt["a"]);var jt=new mt["a"]({icons:{iconfont:"fa"}});s["a"].config.productionTip=!1,s["a"].use(gt["a"]),new s["a"]({router:bt,store:vt,vuetify:jt,render:function(t){return t(f)}}).$mount("#app")},6258:function(t,e,a){"use strict";var s=a("7039"),n=a.n(s);n.a},"6d80":function(t,e,a){t.exports=a.p+"img/logo-dashboard.0ddf9496.png"},"6f08":function(t,e,a){},7039:function(t,e,a){},"7c55":function(t,e,a){"use strict";var s=a("6f08"),n=a.n(s);n.a},b463:function(t,e,a){t.exports=a.p+"img/logo-login.68e4f711.png"},b6a6:function(t,e,a){"use strict";var s=a("3a34"),n=a.n(s);n.a},c832:function(t,e,a){},ffa5:function(t,e,a){"use strict";var s=a("c832"),n=a.n(s);n.a}});
//# sourceMappingURL=app.2732168f.js.map