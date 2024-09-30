import{_ as $}from"./AuthenticatedLayout-Db8LswDm.js";import{r as x,o as b,b as o,c as d,F as f,g as k,h as w,w as y,d as e,t as a,f as r,u as n,Q as g,i as V,a as m,Z as C,e as _}from"./app-Cb4zh1as.js";import{d as i}from"./dayjs.min-zVbtPBEB.js";import{r as T}from"./relativeTime-cVjl5Hze.js";import{u as S}from"./utc-VLRSRYp2.js";import{P as B}from"./PaginatorComponent-BxBnWDn5.js";import"./ApplicationLogo-DjxOrcEz.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./PrimaryButton-D8Kfi4-w.js";import"./SecondaryButton-urNcfTIs.js";const N={key:0},M={class:"border-b flex justify-between items-center py-2 sm:p-2"},I={class:"text-xs"},j={key:0,class:"font-bold text-sm"},P={key:1,class:"font-bold text-sm"},q={key:2,class:"text-red-500"},A={key:3},D={key:4},E={class:"flex justify-end items-center mt-4"},F={class:"w-full lg:w-1/3"},L={__name:"MyTeamVouchersComponent",props:{voucherSetId:{required:!1,default:null}},setup(h){i.extend(T),i.extend(S);const v=h,s=x({}),p=x(5);b(()=>{c()});function c(t=1){axios.get("/my-team-vouchers?cached=false&where[]=voucher_set_id,"+v.voucherSetId+"&page="+t+"&limit="+p.value+"&relations=createdByTeam,allocatedToServiceTeam").then(u=>{s.value=u.data.data}).catch(u=>{console.log(u)})}return(t,u)=>s.value.data&&s.value.data.length?(o(),d("div",N,[(o(!0),d(f,null,k(s.value.data,l=>(o(),w(n(V),{href:t.route("voucher",l.id),class:"hover:no-underline hover:opacity-75"},{default:y(()=>[e("div",M,[e("div",I,[l.voucher_short_code?(o(),d("div",j," #"+a(l.voucher_short_code),1)):(o(),d("div",P," #"+a(l.id),1)),l.is_test?(o(),d("div",q," Test voucher ")):r("",!0),l.created_by_team&&l.created_by_team_id!==n(g)().props.auth.user.current_team_id?(o(),d("div",A," Created by: "+a(l.created_by_team.name),1)):r("",!0),l.allocated_to_service_team&&l.allocated_to_service_team_id!==n(g)().props.auth.user.current_team_id?(o(),d("div",D," Allocated to: "+a(l.allocated_to_service_team.name),1)):r("",!0)]),u[0]||(u[0]=e("div",null,[e("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor",class:"size-6"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m8.25 4.5 7.5 7.5-7.5 7.5"})])],-1))])]),_:2},1032,["href"]))),256)),e("div",E,[e("div",F,[m(B,{onSetDataPage:c,"pagination-data":s.value},null,8,["pagination-data"])])])])):r("",!0)}},R={class:"card"},z={key:0,class:"font-bold text-red-500 text-sm"},Q={class:"card"},Z={class:"grid grid-cols-4 gap-y-12 text-center mt-8"},G={class:"font-bold text-3xl"},H={class:"font-bold text-3xl"},J={class:"font-bold text-3xl"},K={class:"font-bold text-3xl"},O={class:"font-bold text-3xl"},U={key:0},W={class:"font-bold text-3xl"},X={class:"text-xs"},Y={key:1},ee={class:"font-bold text-3xl"},te={class:"text-xs"},se={class:"card"},ae={key:0},oe={key:1,class:"text-xs mt-2"},de={class:"card"},le={key:0},re={class:"card"},ie={key:0},ne={class:"list-disc ml-4"},ue={class:"card"},be={__name:"VoucherSet",props:{voucherSetId:{type:String,required:!1}},setup(h){i.extend(T),i.extend(S);const v=h,s=x({});b(()=>{p()});function p(){axios.get("/my-team-voucher-sets/"+v.voucherSetId+"?cached=false&relations=createdByTeam,allocatedToServiceTeam,voucherSetMerchantTeams.merchantTeam").then(c=>{s.value=c.data.data}).catch(c=>{console.log(c)})}return(c,t)=>(o(),d(f,null,[m(n(C),{title:"Voucher Set"}),m($,null,{header:y(()=>t[0]||(t[0]=[e("h2",{class:"font-normal text-xl text-gray-800 leading-tight"},"Voucher Set",-1)])),default:y(()=>[e("div",R,[e("h2",null,a(s.value.id),1),s.value.is_test?(o(),d("div",z," Test voucher set ")):r("",!0)]),e("div",Q,[t[8]||(t[8]=e("div",{class:"card-header"}," Voucher set details ",-1)),e("div",Z,[e("div",null,[e("div",G," $"+a(s.value.total_set_value/100),1),t[1]||(t[1]=_(" Total set value "))]),e("div",null,[e("div",H," $"+a(s.value.total_set_value_remaining/100),1),t[2]||(t[2]=_(" Total remaining value "))]),e("div",null,[e("div",J,a(Math.round((s.value.total_set_value-s.value.total_set_value_remaining)/s.value.total_set_value*1e4)/100)+"% ",1),t[3]||(t[3]=_(" Redeemed percentage "))]),e("div",null,[e("div",K,a(s.value.num_vouchers),1),t[4]||(t[4]=_(" # Vouchers "))]),e("div",null,[e("div",O,a(s.value.num_voucher_redemptions),1),t[5]||(t[5]=_(" # Redemptions "))]),s.value.last_redemption_at?(o(),d("div",U,[t[6]||(t[6]=e("div",null," Last redeemed ",-1)),e("div",W,a(n(i).utc(s.value.last_redemption_at).fromNow()),1),e("div",X," ("+a(n(i)(s.value.last_redemption_at))+") ",1)])):r("",!0),s.value.expires_at?(o(),d("div",Y,[t[7]||(t[7]=e("div",null," Expires ",-1)),e("div",ee,a(n(i).utc(s.value.expires_at).fromNow()),1),e("div",te," ("+a(n(i)(s.value.expires_at))+") ",1)])):r("",!0)])]),e("div",se,[t[9]||(t[9]=e("div",{class:"card-header"}," Created by team ",-1)),s.value.created_by_team?(o(),d("div",ae,a(s.value.created_by_team.name),1)):r("",!0),s.value.created_at?(o(),d("div",oe," Created at: "+a(n(i).utc(s.value.created_at).fromNow())+" ("+a(n(i)(s.value.created_at))+") ",1)):r("",!0)]),e("div",de,[t[10]||(t[10]=e("div",{class:"card-header"}," Allocated To Service Team ",-1)),s.value.allocated_to_service_team?(o(),d("div",le,a(s.value.allocated_to_service_team.name),1)):r("",!0)]),e("div",re,[t[11]||(t[11]=e("div",{class:"card-header"}," Merchants ",-1)),s.value.voucher_set_merchant_teams?(o(),d("div",ie,[(o(!0),d(f,null,k(s.value.voucher_set_merchant_teams,u=>(o(),d("ul",ne,[e("li",null,a(u.merchant_team.name),1)]))),256))])):r("",!0)]),e("div",ue,[t[12]||(t[12]=e("div",{class:"card-header"}," Vouchers ",-1)),m(L,{"voucher-set-id":v.voucherSetId},null,8,["voucher-set-id"])]),t[13]||(t[13]=e("div",{class:"pb-32"},null,-1))]),_:1})],64))}};export{be as default};