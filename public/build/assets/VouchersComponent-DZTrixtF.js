import{r as f,o as h,a as t,b as o,F as p,j as g,k as y,w as k,e as a,t as r,h as l,u,i as w,d as x}from"./app-C6-ooqD9.js";import{P as b}from"./PaginatorComponent-CPEQ2q4X.js";import{d as n}from"./dayjs.min-BVj6BrBB.js";import{r as B}from"./relativeTime-DK8zQSks.js";import{u as I}from"./utc-BqjOqHj6.js";const V={key:0},C={class:"border-b flex justify-between items-center py-2 sm:p-2"},S={class:"text-xs"},j={key:0,class:"font-bold text-sm"},T={key:1,class:"font-bold text-sm"},N={key:2,class:"text-red-500"},q={key:3},P={key:4},$={key:5},D={class:"flex justify-end items-center mt-4"},F={class:"w-full lg:w-1/3"},O={__name:"VouchersComponent",props:{teamId:{required:!1,default:null},filterVouchers:{required:!1,default:null},voucherSetId:{required:!1,default:null}},setup(v){n.extend(B),n.extend(I);const s=v,_=f(50),d=f({});h(()=>{(s.teamId||s.voucherSetId)&&(_.value=10),m()});function m(c=1){let i="";s.teamId&&s.filterVouchers?i="&where[]="+s.filterVouchers+","+s.teamId:s.voucherSetId&&(i="&where[]=voucher_set_id,"+s.voucherSetId),axios.get("/admin/vouchers?cached=false&page="+c+"&limit="+_.value+i+"&orderBy=created_at,desc&relations=createdByTeam,allocatedToServiceTeam").then(e=>{d.value=e.data.data}).catch(e=>{console.log(e)})}return(c,i)=>d.value.data&&d.value.data.length?(t(),o("div",V,[(t(!0),o(p,null,g(d.value.data,e=>(t(),y(u(w),{href:c.route("admin.voucher",e.id),class:"hover:no-underline hover:opacity-75"},{default:k(()=>[a("div",C,[a("div",S,[e.voucher_short_code?(t(),o("div",j," #"+r(e.voucher_short_code),1)):(t(),o("div",T," #"+r(e.id),1)),e.is_test?(t(),o("div",N," Test voucher ")):l("",!0),e.created_by_team?(t(),o("div",q," Created by: "+r(e.created_by_team.name),1)):l("",!0),e.allocated_to_service_team?(t(),o("div",P," Allocated to: "+r(e.allocated_to_service_team.name),1)):l("",!0),a("div",null," Original value: $"+r(e.voucher_value_original/100),1),a("div",null," Remaining value: $"+r(e.voucher_value_remaining/100),1),e.created_at?(t(),o("div",$," Created at: "+r(u(n).utc(e.created_at).fromNow())+" ("+r(u(n)(e.created_at))+") ",1)):l("",!0)]),i[0]||(i[0]=a("div",null,[a("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor",class:"size-6"},[a("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m8.25 4.5 7.5 7.5-7.5 7.5"})])],-1))])]),_:2},1032,["href"]))),256)),a("div",D,[a("div",F,[x(b,{onSetDataPage:m,"pagination-data":d.value},null,8,["pagination-data"])])])])):l("",!0)}};export{O as _};