import{_ as p}from"./AuthenticatedLayout-R47MbXuc.js";import{o as m,c as h,a as g,b as e,r as x,d as s,w as t,u as o,i,t as r,F as w,Z as y}from"./app-D7vdOgIt.js";import{S as $,_ as j}from"./AdminTopNavigation-Fmss29Es.js";import{_ as c}from"./SecondaryButton-Lp9hBCYQ.js";import"./ApplicationLogo-D1AC-3fn.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";const S={key:0,class:"-ml-12 -mt-12"},V=e("svg",{class:"animate-spin -ml-1 mr-3 h-8 w-8",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},[e("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"}),e("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"})],-1),k=[V],B={__name:"AjaxLoadingIndicator",props:{loading:{type:Boolean,required:!0,default:!1}},setup(_){const d=_;return(l,f)=>d.loading?(m(),h("div",S,k)):g("",!0)}},C={class:"card"},b={class:"grid grid-cols-2 lg:grid-cols-4 gap-4"},N={class:"w-full"},A={class:"flex justify-center text-3xl"},D=e("div",{class:"text-xs"}," # Users ",-1),F={class:"w-full"},R={class:"flex justify-center text-3xl"},z=e("div",{class:"text-xs"}," # Teams ",-1),H=e("div",{class:"hidden lg:inline"},null,-1),I=e("div",{class:"hidden lg:inline"},null,-1),O={class:"w-full"},q={class:"flex justify-center text-3xl"},E=e("div",{class:"text-xs"}," # Voucher Sets (x) ",-1),L={class:"w-full"},M={class:"flex justify-center text-3xl"},T=e("div",{class:"text-xs"}," # Vouchers (x) ",-1),U={class:"w-full"},X={class:"flex justify-center text-3xl"},Y=e("div",{class:"text-xs"}," $ Voucher (Original) (x) ",-1),Z={class:"w-full"},G={class:"flex justify-center text-3xl"},J=e("div",{class:"text-xs"}," $ Vouchers Remaining (x) ",-1),K={class:"w-full"},P={class:"flex justify-center text-3xl"},Q=e("div",{class:"text-xs"}," # Redemptions (x) ",-1),W={class:"w-full"},ee={class:"flex justify-center text-3xl"},se=e("div",{class:"text-xs"}," $ Redemptions (x) ",-1),te=e("div",null," X = Yet to be linked ",-1),ae={__name:"SystemStatisticsComponent",setup(_){const d=x(!1),l=x({num_users:0,num_teams:0,num_voucher_sets:0,num_vouchers:0,num_voucher_redemptions:0,sum_voucher_value_total:0,sum_voucher_value_redeemed:0,sum_voucher_value_remaining:0});function f(){d.value=!0,axios.get("/admin/system-statistics?cached=false&limit=1&orderBy=id,desc").then(a=>{var u,v;(u=a.data.data)!=null&&u.data[0]&&(l.value=(v=a.data.data)==null?void 0:v.data[0]),d.value=!1}).catch(a=>{$.fire({icon:"error",title:"Oops!",text:a.response.data.meta.message}),d.value=!1})}function n(a){return Intl.NumberFormat("en",{notation:"compact"}).format(a)}return f(),(a,u)=>(m(),h("div",C,[s(B,{loading:d.value},null,8,["loading"]),e("div",b,[s(c,null,{default:t(()=>[e("div",N,[s(o(i),{href:a.route("admin.users"),class:"hover:no-underline"},{default:t(()=>[e("div",A,r(n(l.value.num_users)),1),D]),_:1},8,["href"])])]),_:1}),s(c,null,{default:t(()=>[e("div",F,[s(o(i),{href:a.route("admin.teams"),class:"hover:no-underline"},{default:t(()=>[e("div",R,r(n(l.value.num_teams)),1),z]),_:1},8,["href"])])]),_:1}),H,I,s(c,null,{default:t(()=>[e("div",O,[s(o(i),{href:a.route("admin.teams"),class:"hover:no-underline"},{default:t(()=>[e("div",q,r(n(l.value.num_voucher_sets)),1),E]),_:1},8,["href"])])]),_:1}),s(c,null,{default:t(()=>[e("div",L,[s(o(i),{href:a.route("admin.teams"),class:"hover:no-underline"},{default:t(()=>[e("div",M,r(n(l.value.num_vouchers)),1),T]),_:1},8,["href"])])]),_:1}),s(c,null,{default:t(()=>[e("div",U,[s(o(i),{href:a.route("admin.teams"),class:"hover:no-underline"},{default:t(()=>[e("div",X,r(n(l.value.sum_voucher_value_total)),1),Y]),_:1},8,["href"])])]),_:1}),s(c,null,{default:t(()=>[e("div",Z,[s(o(i),{href:a.route("admin.teams"),class:"hover:no-underline"},{default:t(()=>[e("div",G,r(n(l.value.sum_voucher_value_remaining)),1),J]),_:1},8,["href"])])]),_:1}),s(c,null,{default:t(()=>[e("div",K,[s(o(i),{href:a.route("admin.teams"),class:"hover:no-underline"},{default:t(()=>[e("div",P,r(n(l.value.num_voucher_redemptions)),1),Q]),_:1},8,["href"])])]),_:1}),s(c,null,{default:t(()=>[e("div",W,[s(o(i),{href:a.route("admin.teams"),class:"hover:no-underline"},{default:t(()=>[e("div",ee,r(n(l.value.sum_voucher_value_redeemed)),1),se]),_:1},8,["href"])])]),_:1}),te])]))}},de={__name:"AdminHome",setup(_){return(d,l)=>(m(),h(w,null,[s(o(y),{title:"Dashboard"}),s(p,null,{header:t(()=>[s(j)]),default:t(()=>[s(ae)]),_:1})],64))}};export{de as default};