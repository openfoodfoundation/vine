import{_ as p}from"./AuthenticatedLayout-DSVsSXG6.js";import{r as f,o as h,a,b as n,d as r,u,w as c,F as _,Z as v,e,j as g,h as m,k as x,i as k,t as d,f as w}from"./app-ClCJcWW-.js";import{_ as y}from"./AdminTopNavigation-yTEVNZVX.js";import{P as A}from"./PaginatorComponent-Bu2dN7Jx.js";import"./ApplicationLogo-BahlM8sz.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./sweetalert2.all-BxHbwWoH.js";import"./SecondaryButton-Bv260Y3G.js";const B={class:"card"},b={key:0},P={class:"border-b flex justify-between items-center py-2 sm:p-2"},T={class:"font-bold"},j={class:"text-xs opacity-25"},C={key:0,class:"text-sm"},N=e("div",{class:"text-2xl"},[e("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor",class:"size-6"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m8.25 4.5 7.5 7.5-7.5 7.5"})])],-1),V={class:"flex justify-end items-center mt-4"},I={class:"w-full lg:w-1/3"},q={__name:"APIAccessTokens",setup(D){const s=f({});h(()=>{l()});function l(i=1){axios.get("/admin/user-personal-access-tokens?cached=false&page="+i+"&relations=user&orderBy=id,desc").then(o=>{s.value=o.data.data}).catch(o=>{console.log(o)})}return(i,o)=>(a(),n(_,null,[r(u(v),{title:"API Access Tokens"}),r(p,null,{header:c(()=>[r(y)]),default:c(()=>[e("div",B,[s.value.data&&s.value.data.length?(a(),n("div",b,[(a(!0),n(_,null,g(s.value.data,t=>(a(),x(u(k),{href:i.route("admin.api-access-token",t.id),class:"hover:no-underline hover:opacity-75"},{default:c(()=>[e("div",P,[e("div",null,[e("div",T,[e("span",j," #"+d(t.id),1),w(" "+d(t.name),1)]),t.user?(a(),n("div",C," Issued to: "+d(t.user.name),1)):m("",!0)]),N])]),_:2},1032,["href"]))),256))])):m("",!0),e("div",V,[e("div",I,[r(A,{onSetDataPage:l,"pagination-data":s.value},null,8,["pagination-data"])])])])]),_:1})],64))}};export{q as default};