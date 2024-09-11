import{a as s,b as n,e as t,t as $,r as f,d as i,l as F,m as q,F as S,j as A,h as w,o as z,w as g,f as y,u as N,i as V,n as K,Z as O}from"./app-BmSBEPLV.js";import{_ as W}from"./AuthenticatedLayout-DdfZyy7S.js";import{_ as L}from"./AdminTopNavigation-zKgKSmvv.js";import{P as B}from"./PaginatorComponent-B2sO_2eZ.js";import{S as T}from"./sweetalert2.esm.all-BccGxJ0c.js";import{P as I}from"./PrimaryButton-CAgfJv0H.js";import{_ as D}from"./TextInput-hXLQ3rIk.js";import{_ as U}from"./InputLabel-BNUlAfJX.js";import{_ as E}from"./AdminTeamCreateComponent-C8IVGLmI.js";import{_ as R}from"./AdminUserDetailsComponent-CAjQUKBU.js";import{_ as Y}from"./SecondaryButton-Ct9Movp8.js";import{_ as Z}from"./AjaxLoadingIndicator-DDUm2-mU.js";import{d as P}from"./dayjs.min-qJ3qdoNV.js";import{r as G}from"./relativeTime-Cl4CwFPZ.js";import{u as H}from"./utc-D3jO9Eex.js";import{D as J}from"./DangerButton-BTr8qyOo.js";import"./ApplicationLogo-D4VsQ7HS.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";const Q={class:"flex justify-start items-center"},X={class:"text-xs mr-2"},ee={class:""},M={__name:"AdminTeamDetailsComponent",props:{team:{required:!0,type:Object}},setup(_){const v=_;return(h,c)=>(s(),n("div",Q,[t("div",X,"#"+$(v.team.id),1),t("div",ee,$(v.team.name),1)]))}},te={key:0},ae={key:1},se={key:0,class:"mt-4"},ne={class:"border-b py-1"},ie=["onClick"],le={key:0,class:"text-red-500 text-xs italic pl-2"},oe={key:1},de={__name:"AdminTeamMerchantTeamSelectComponent",props:{teamId:{required:!1,default:null}},emits:["teamSelected"],setup(_,{emit:v}){const h=_,c=f(!1),l=f(""),o=f({}),m=v;function C(){axios.get("/admin/teams?where[]=name,like,*"+l.value+"*&limit=100&relations=teamsThisTeamIsMerchantFor").then(u=>{o.value=u.data.data}).catch(u=>{console.log(u)})}function k(){c.value=!0,o.value={}}function p(u){x(u)}function x(u){m("teamSelected",u),l.value="",o.value={}}function b(u){return u.teams_this_team_is_merchant_for.find(r=>r.team_id===h.teamId)}return(u,r)=>c.value?(s(),n("div",te,[i(E,{searchStr:l.value,onTeamCreated:p},null,8,["searchStr"])])):(s(),n("div",ae,[t("div",null,[i(U,{for:"name",value:"Team name(Type to search and press Enter)"}),i(D,{onKeyup:r[0]||(r[0]=F(q(a=>C(),["prevent"]),["enter"])),id:"name",type:"text",class:"mt-1 block w-full",modelValue:l.value,"onUpdate:modelValue":r[1]||(r[1]=a=>l.value=a),required:""},null,8,["modelValue"])]),l.value.length>0&&o.value.total>0?(s(),n("div",se,[(s(!0),n(S,null,A(o.value.data,a=>(s(),n("div",ne,[t("button",{onClick:e=>x(a),class:"flex justify-start items-end"},[i(M,{team:a},null,8,["team"]),b(a)?(s(),n("span",le,"***Already added")):w("",!0)],8,ie)]))),256)),t("div",{class:"text-red-500 text-sm mt-4 cursor-pointer hover:underline",onClick:r[2]||(r[2]=a=>k())}," Create a new team? ")])):w("",!0),l.value.length>0&&o.value.total===0?(s(),n("div",oe,[t("div",{class:"text-red-500 text-sm mt-4 cursor-pointer hover:underline",onClick:r[3]||(r[3]=a=>k())}," We could not find teams. Do you want to create a new team? ")])):w("",!0)]))}},re={class:"grid grid-cols-1 md:grid-cols-2 gap-0 md:gap-4"},ue={class:"card"},me={class:"card-header flex justify-between items-center"},ce={class:"text-xs italic"},ve={class:""},fe={class:"flex justify-end"},_e={key:0},he={key:1},pe={key:0},xe={key:1},ge={class:"py-2"},ye={class:"font-bold"},we={key:2},$e={key:0,class:"mb-8"},ke={class:"border-b py-1 flex justify-between items-end"},be=["onClick"],Ce={class:"flex justify-end items-center mt-4"},Te={class:"w-full lg:w-1/3"},Se={class:"card"},Ne={class:"card-header"},Ie={class:"text-xs italic"},Ae={key:0,class:"mb-8"},je={class:"border-b py-1 flex justify-between items-end"},Ve=["onClick"],De={class:"flex justify-end items-center mt-4"},Be={class:"w-full lg:w-1/3"},Me={__name:"AdminTeamMerchantTeamsComponent",props:{teamId:{required:!0,type:Number},teamName:{required:!0}},setup(_){const v=_,h=f(!1),c=f(!1),l=f({}),o=f({}),m=f({});z(()=>{p(),x()});function C(){h.value=!0}function k(){h.value=!1,c.value=!1,m.value={}}function p(a=1){axios.get("/admin/team-merchant-teams?cached=false&where[]=team_id,"+v.teamId+"&page="+a+"&relations=merchantTeam").then(e=>{l.value=e.data.data}).catch(e=>{console.log(e)})}function x(a=1){axios.get("/admin/team-merchant-teams?cached=false&where[]=merchant_team_id,"+v.teamId+"&page="+a+"&relations=team").then(e=>{o.value=e.data.data}).catch(e=>{console.log(e)})}function b(a){T.fire({title:"Are you sure you want to delete?",text:"This action cannot be undone. Please confirm if you wish to proceed.",icon:"warning",confirmButtonColor:"#3085d6",confirmButtonText:"Delete merchant team",showCancelButton:!0}).then(e=>{e.isConfirmed&&axios.delete("/admin/team-merchant-teams/"+a).then(d=>{T.fire({title:"Success!",icon:"success",timer:1e3}).then(()=>{p(),x()})}).catch(d=>{console.log(d)})})}function u(){let a={team_id:v.teamId,merchant_team_id:m.value.id};axios.post("/admin/team-merchant-teams",a).then(e=>{T.fire({title:"Success!",icon:"success",timer:1e3}).then(()=>{m.value={},c.value=!1,p(),x()})}).catch(e=>{console.log(e)})}function r(a){m.value=a,h.value=!1,c.value=!0}return(a,e)=>(s(),n("div",re,[t("div",ue,[t("div",me,[t("div",null,[e[3]||(e[3]=t("div",null," Merchant teams ",-1)),t("div",ce," These teams may redeem vouchers for "+$(_.teamName),1)]),t("div",ve,[t("div",fe,[!h.value&&!c.value?(s(),n("div",_e,[i(I,{onClick:e[0]||(e[0]=d=>C()),class:"ms-4"},{default:g(()=>e[4]||(e[4]=[y(" Add Merchant Team ")])),_:1})])):(s(),n("div",he,[i(I,{onClick:e[1]||(e[1]=d=>k()),class:"ms-4"},{default:g(()=>e[5]||(e[5]=[y(" Cancel ")])),_:1})]))])])]),h.value?(s(),n("div",pe,[e[6]||(e[6]=t("div",{class:"py-2"},"Select merchant team...",-1)),i(de,{teamId:_.teamId,onTeamSelected:r},null,8,["teamId"])])):c.value?(s(),n("div",xe,[t("div",ge,[e[7]||(e[7]=y("Add ")),t("span",ye,$(m.value.name),1),e[8]||(e[8]=y(" as merchant team? "))]),i(I,{onClick:e[2]||(e[2]=d=>u()),class:""},{default:g(()=>e[9]||(e[9]=[y(" Add ")])),_:1})])):(s(),n("div",we,[l.value.data&&l.value.data.length?(s(),n("div",$e,[(s(!0),n(S,null,A(l.value.data,d=>(s(),n("div",ke,[i(N(V),{href:a.route("admin.team",d.merchant_team_id)},{default:g(()=>[i(M,{team:d.merchant_team},null,8,["team"])]),_:2},1032,["href"]),t("button",{onClick:j=>b(d.id),class:"text-xs text-red-500 flex items-center"},e[10]||(e[10]=[t("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"1.5",class:"size-3"},[t("line",{x1:"18",y1:"6",x2:"6",y2:"18"}),t("line",{x1:"6",y1:"6",x2:"18",y2:"18"})],-1),y(" Delete ")]),8,be)]))),256)),t("div",Ce,[t("div",Te,[i(B,{onSetDataPage:p,"pagination-data":l.value},null,8,["pagination-data"])])])])):w("",!0)]))]),t("div",Se,[t("div",Ne,[t("div",null," Teams "+$(_.teamName)+" is merchant for ",1),t("div",Ie,$(_.teamName)+" may redeem vouchers for these teams ",1)]),o.value.data&&o.value.data.length?(s(),n("div",Ae,[(s(!0),n(S,null,A(o.value.data,d=>(s(),n("div",je,[i(N(V),{href:a.route("admin.team",d.team_id)},{default:g(()=>[i(M,{team:d.team},null,8,["team"])]),_:2},1032,["href"]),t("button",{onClick:j=>b(d.id),class:"text-xs text-red-500 flex items-center"},e[11]||(e[11]=[t("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"1.5",class:"size-3"},[t("line",{x1:"18",y1:"6",x2:"6",y2:"18"}),t("line",{x1:"6",y1:"6",x2:"18",y2:"18"})],-1),y(" Delete ")]),8,Ve)]))),256)),t("div",De,[t("div",Be,[i(B,{onSetDataPage:x,"pagination-data":o.value},null,8,["pagination-data"])])])])):w("",!0)])]))}},Ue={key:0},qe={key:1},Pe={key:0,class:"mt-4"},ze={class:"border-b py-1"},Fe=["onClick"],Ee={key:0,class:"text-red-500 text-xs italic pl-2"},Ke={key:1},Oe={__name:"AdminTeamServiceTeamSelectComponent",props:{teamId:{required:!1,default:null}},emits:["teamSelected"],setup(_,{emit:v}){const h=_,c=f(!1),l=f(""),o=f({}),m=v;function C(){axios.get("/admin/teams?where[]=name,like,*"+l.value+"*&limit=100&relations=teamsThisTeamIsServiceFor").then(u=>{o.value=u.data.data}).catch(u=>{console.log(u)})}function k(){c.value=!0,o.value={}}function p(u){x(u)}function x(u){m("teamSelected",u),l.value="",o.value={}}function b(u){return u.teams_this_team_is_service_for.find(r=>r.team_id===h.teamId)}return(u,r)=>c.value?(s(),n("div",Ue,[i(E,{searchStr:l.value,onTeamCreated:p},null,8,["searchStr"])])):(s(),n("div",qe,[t("div",null,[i(U,{for:"name",value:"Team name(Type to search and press Enter)"}),i(D,{onKeyup:r[0]||(r[0]=F(q(a=>C(),["prevent"]),["enter"])),id:"name",type:"text",class:"mt-1 block w-full",modelValue:l.value,"onUpdate:modelValue":r[1]||(r[1]=a=>l.value=a),required:""},null,8,["modelValue"])]),l.value.length>0&&o.value.total>0?(s(),n("div",Pe,[(s(!0),n(S,null,A(o.value.data,a=>(s(),n("div",ze,[t("button",{onClick:e=>x(a),class:"flex justify-start items-end"},[i(M,{team:a},null,8,["team"]),b(a)?(s(),n("span",Ee,"***Already added")):w("",!0)],8,Fe)]))),256)),t("div",{class:"text-red-500 text-sm mt-4 cursor-pointer hover:underline",onClick:r[2]||(r[2]=a=>k())}," Create a new team? ")])):w("",!0),l.value.length>0&&o.value.total===0?(s(),n("div",Ke,[t("div",{class:"text-red-500 text-sm mt-4 cursor-pointer hover:underline",onClick:r[3]||(r[3]=a=>k())}," We could not find teams. Do you want to create a new team? ")])):w("",!0)]))}},We={class:"grid grid-cols-1 md:grid-cols-2 gap-0 md:gap-4"},Le={class:"card"},Re={class:"card-header flex justify-between items-center"},Ye={class:"text-xs italic"},Ze={class:""},Ge={class:"flex justify-end"},He={class:"flex justify-end"},Je={key:0},Qe={key:1},Xe={key:0},et={key:1},tt={class:"py-2"},at={class:"font-bold"},st={key:2},nt={key:0,class:"mb-8"},it={class:"border-b py-1 flex justify-between items-end"},lt=["onClick"],ot={class:"flex justify-end items-center mt-4"},dt={class:"w-full lg:w-1/3"},rt={class:"card"},ut={class:"card-header"},mt={class:"text-xs italic"},ct={key:0,class:"mb-8"},vt={class:"border-b py-1 flex justify-between items-end"},ft=["onClick"],_t={class:"flex justify-end items-center mt-4"},ht={class:"w-full lg:w-1/3"},pt={__name:"AdminTeamServiceTeamsComponent",props:{teamId:{required:!0,type:Number},teamName:{required:!0}},setup(_){const v=_,h=f(!1),c=f(!1),l=f({}),o=f({}),m=f({});z(()=>{p(),x()});function C(){h.value=!0}function k(){h.value=!1,c.value=!1,m.value={}}function p(a=1){axios.get("/admin/team-service-teams?cached=false&where[]=team_id,"+v.teamId+"&page="+a+"&relations=serviceTeam").then(e=>{l.value=e.data.data}).catch(e=>{console.log(e)})}function x(a=1){axios.get("/admin/team-service-teams?cached=false&where[]=service_team_id,"+v.teamId+"&page="+a+"&relations=team").then(e=>{o.value=e.data.data}).catch(e=>{console.log(e)})}function b(a){T.fire({title:"Are you sure you want to delete?",text:"This action cannot be undone. Please confirm if you wish to proceed.",icon:"warning",confirmButtonColor:"#3085d6",confirmButtonText:"Delete service team",showCancelButton:!0}).then(e=>{e.isConfirmed&&axios.delete("/admin/team-service-teams/"+a).then(d=>{T.fire({title:"Success!",icon:"success",timer:1e3}).then(()=>{p(),x()})}).catch(d=>{console.log(d)})})}function u(){let a={team_id:v.teamId,service_team_id:m.value.id};axios.post("/admin/team-service-teams",a).then(e=>{T.fire({title:"Success!",icon:"success",timer:1e3}).then(()=>{m.value={},c.value=!1,p(),x()})}).catch(e=>{console.log(e)})}function r(a){m.value=a,h.value=!1,c.value=!0}return(a,e)=>(s(),n("div",We,[t("div",Le,[t("div",Re,[t("div",null,[e[3]||(e[3]=t("div",null," Service teams ",-1)),t("div",Ye," These teams may distribute vouchers for redemption at "+$(_.teamName),1)]),t("div",Ze,[t("div",Ge,[t("div",He,[!h.value&&!c.value?(s(),n("div",Je,[i(I,{onClick:e[0]||(e[0]=d=>C()),class:"ms-4"},{default:g(()=>e[4]||(e[4]=[y(" Add Service Team ")])),_:1})])):(s(),n("div",Qe,[i(I,{onClick:e[1]||(e[1]=d=>k()),class:"ms-4"},{default:g(()=>e[5]||(e[5]=[y(" Cancel ")])),_:1})]))])])])]),h.value?(s(),n("div",Xe,[e[6]||(e[6]=t("div",{class:"py-2"},"Select service team...",-1)),i(Oe,{teamId:_.teamId,onTeamSelected:r},null,8,["teamId"])])):c.value?(s(),n("div",et,[t("div",tt,[e[7]||(e[7]=y("Adding ")),t("span",at,$(m.value.name),1),e[8]||(e[8]=y(" as service team?"))]),i(I,{onClick:e[2]||(e[2]=d=>u()),class:""},{default:g(()=>e[9]||(e[9]=[y(" Add ")])),_:1})])):(s(),n("div",st,[l.value.data&&l.value.data.length?(s(),n("div",nt,[(s(!0),n(S,null,A(l.value.data,d=>(s(),n("div",it,[i(N(V),{href:a.route("admin.team",d.service_team_id)},{default:g(()=>[i(M,{team:d.service_team},null,8,["team"])]),_:2},1032,["href"]),t("button",{onClick:j=>b(d.id),class:"text-xs text-red-500 flex items-center"},e[10]||(e[10]=[t("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"1.5",class:"size-3"},[t("line",{x1:"18",y1:"6",x2:"6",y2:"18"}),t("line",{x1:"6",y1:"6",x2:"18",y2:"18"})],-1),y(" Delete ")]),8,lt)]))),256)),t("div",ot,[t("div",dt,[i(B,{onSetDataPage:p,"pagination-data":l.value},null,8,["pagination-data"])])])])):w("",!0)]))]),t("div",rt,[t("div",ut,[t("div",null," Teams "+$(_.teamName)+" is service for ",1),t("div",mt,$(_.teamName)+" may distribute vouchers to these teams ",1)]),o.value.data&&o.value.data.length?(s(),n("div",ct,[(s(!0),n(S,null,A(o.value.data,d=>(s(),n("div",vt,[i(N(V),{href:a.route("admin.team",d.team_id)},{default:g(()=>[i(M,{team:d.team},null,8,["team"])]),_:2},1032,["href"]),t("button",{onClick:j=>b(d.id),class:"text-xs text-red-500 flex items-center"},e[11]||(e[11]=[t("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"1.5",class:"size-3"},[t("line",{x1:"18",y1:"6",x2:"6",y2:"18"}),t("line",{x1:"6",y1:"6",x2:"18",y2:"18"})],-1),y(" Delete ")]),8,ft)]))),256)),t("div",_t,[t("div",ht,[i(B,{onSetDataPage:x,"pagination-data":o.value},null,8,["pagination-data"])])])])):w("",!0)])]))}},xt={key:0},gt={class:"flex items-center justify-end mt-4"},yt={key:1},wt={key:0,class:"mt-4"},$t=["onClick"],kt={key:1},bt={__name:"AdminUserSelectComponent",props:{teamId:{required:!0,type:Number}},emits:["createNewTeamUser"],setup(_,{emit:v}){const h=_,c=f(!1),l=f({name:"",email:"",current_team_id:null}),o=f(""),m=f({}),C=v;function k(){l.value.current_team_id=h.teamId,axios.post("admin/users",l.value).then(u=>{let r=u.data.data.id;b(r),l.value={name:"",email:"",current_team_id:null},c.value=!1}).catch(u=>{console.log(u)})}function p(){axios.get("/admin/users?where[]=name,like,*"+o.value+"*&limit=100").then(u=>{m.value=u.data.data}).catch(u=>{console.log(u)})}function x(){c.value=!0,m.value={},l.value.name=o.value}function b(u){C("createNewTeamUser",u),o.value="",m.value={}}return(u,r)=>c.value?(s(),n("div",xt,[t("div",null,[i(U,{for:"name",value:"Name"}),i(D,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:l.value.name,"onUpdate:modelValue":r[0]||(r[0]=a=>l.value.name=a),required:""},null,8,["modelValue"])]),t("div",null,[i(U,{for:"email",value:"Email"}),i(D,{id:"email",type:"email",class:"mt-1 block w-full",modelValue:l.value.email,"onUpdate:modelValue":r[1]||(r[1]=a=>l.value.email=a),required:""},null,8,["modelValue"])]),t("div",gt,[i(I,{onClick:r[2]||(r[2]=q(a=>k(),["prevent"])),class:K(["ms-4",{"opacity-25":!l.value.name||!l.value.email}]),disabled:!l.value.name||!l.value.email},{default:g(()=>r[7]||(r[7]=[y(" Submit ")])),_:1},8,["class","disabled"])])])):(s(),n("div",yt,[t("div",null,[i(U,{for:"name",value:"Find A User"}),i(D,{onKeyup:r[3]||(r[3]=a=>p()),modelValue:o.value,"onUpdate:modelValue":r[4]||(r[4]=a=>o.value=a),class:"mt-1 block w-full",placeholder:"Search by name..",type:"text"},null,8,["modelValue"])]),o.value.length>0&&m.value.total>0?(s(),n("div",wt,[(s(!0),n(S,null,A(m.value.data,a=>(s(),n("a",{href:"#",onClick:e=>b(a.id),class:"border-b py-1",tabindex:"0"},[i(R,{user:a},null,8,["user"])],8,$t))),256)),t("div",{class:"text-red-500 text-sm mt-4 cursor-pointer hover:underline",onClick:r[5]||(r[5]=a=>x())}," Create a new user? ")])):w("",!0),o.value.length>0&&m.value.total===0?(s(),n("div",kt,[t("div",{class:"text-red-500 text-sm mt-4 cursor-pointer hover:underline",onClick:r[6]||(r[6]=a=>x())}," We could not find users. Do you want to create a new user? ")])):w("",!0)]))}},Ct={class:"card"},Tt={class:"card-header flex justify-between"},St={class:"grid gap-4 grid-cols-6 mt-8"},Nt={key:0},It={key:1},At=["src"],jt={class:"flex justify-end items-center mt-4"},Vt={class:"w-full lg:w-1/3"},Dt={__name:"AdminTeamVoucherTemplatesList",props:{team:{type:Object,required:!0}},setup(_){const v=_,h=f({});function c(l=1){var o;axios.get("/admin/team-voucher-templates?cached=false&where[]=team_id,"+((o=v.team)==null?void 0:o.id)+"&page="+l).then(m=>{h.value=m.data.data}).catch(m=>{T.fire({icon:"error",title:"Oops!",text:m.response.data.message})})}return c(),(l,o)=>(s(),n("div",Ct,[t("div",Tt,[o[1]||(o[1]=t("div",null," Voucher Templates ",-1)),t("div",null,[i(N(V),{href:"/admin/team-voucher-templates/new?teamId="+_.team.id},{default:g(()=>[i(I,{class:""},{default:g(()=>o[0]||(o[0]=[t("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor",class:"size-6"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M12 4.5v15m7.5-7.5h-15"})],-1),y(" New Template ")])),_:1})]),_:1},8,["href"])])]),t("div",St,[(s(!0),n(S,null,A(h.value.data,m=>(s(),n("div",null,[m.archived_at?(s(),n("div",Nt,o[2]||(o[2]=[t("div",{class:"border-2 text-center rounded-lg border-dashed p-2 border-red-300 text-red-300 font-bold"}," Archived ",-1)]))):(s(),n("div",It,o[3]||(o[3]=[t("div",{class:"border-2 text-center rounded-lg border-dashed p-2 border-green-300 text-green-300 font-bold"}," Active ",-1)]))),i(N(V),{href:"/admin/team-voucher-template/"+m.id},{default:g(()=>[t("img",{src:m.example_template_image_url,alt:"",class:"border rounded"},null,8,At)]),_:2},1032,["href"])]))),256))]),t("div",jt,[t("div",Vt,[i(B,{onSetDataPage:c,"pagination-data":h.value},null,8,["pagination-data"])])])]))}},Bt={class:"card"},Mt={class:""},Ut={class:"card"},qt={class:"flex justify-start items-center"},Pt={class:"text-xs mr-2"},zt={key:0,class:"mt-2 flex justify-end"},Ft={class:"card"},Et={key:0},Kt={class:"flex justify-between items-center hover:opacity-75"},Ot={class:"flex justify-end items-center"},Wt={key:0,class:"pr-2 text-xs"},Lt={class:"flex"},Rt={key:0},Yt={key:1,class:"px-2"},Zt={key:2,class:"px-2"},Gt={class:"flex justify-end items-center mt-4"},Ht={class:"w-full lg:w-1/3"},Jt={class:"card"},Qt={class:"container mx-auto"},Xt={class:"container mx-auto"},ea={key:0,class:"container mx-auto"},ga={__name:"Team",props:{id:{required:!0,type:Number}},setup(_){P.extend(G),P.extend(H);const v=_,h=f(10),c=f(""),l=f({}),o=f({}),m=f(!1);z(()=>{k(),p()});function C(a){let e={user_id:a,team_id:v.id};axios.post("/admin/team-users",e).then(d=>{T.fire({title:"Success!",icon:"success",timer:1e3}).then(()=>{p()})}).catch(d=>{console.log(d)})}function k(){axios.get("/admin/teams/"+v.id+"?cached=false").then(a=>{l.value=a.data.data,c.value=l.value.name}).catch(a=>{console.log(a)})}function p(a=1){axios.get("/admin/team-users?cached=false&page="+a+"&where[]=team_id,"+v.id+"&relations=user&limit="+h.value+"&orderBy=id,desc").then(e=>{o.value=e.data.data}).catch(e=>{console.log(e)})}function x(a){m.value=!0;let e={send_invite_email:!0};axios.put("/admin/team-users/"+a.id,e).then(d=>{p(),m.value=!1}).catch(d=>{console.log(d),m.value=!1})}function b(a){T.fire({icon:"warning",title:"Are you sure?",text:"This will remove this user from this team. You can always add them back.",showConfirmButton:!0,showCancelButton:!0}).then(e=>{e.isConfirmed&&axios.delete("/admin/team-users/"+a.id).then(d=>{T.fire({title:"Success!",icon:"success",timer:1e3}).then(()=>{p()})}).catch(d=>{console.log(d)})})}function u(a){p(a)}function r(){let a={name:c.value};axios.put("/admin/teams/"+v.id,a).then(e=>{T.fire({title:"Success!",icon:"success",timer:1e3}).then(()=>{k()})}).catch(e=>{console.log(e)})}return(a,e)=>(s(),n(S,null,[i(N(O),{title:"Team"}),i(W,null,{header:g(()=>[i(L)]),default:g(()=>[t("div",Bt,[t("div",Mt,[t("h2",null,$(l.value.name),1),t("div",null,"#"+$(v.id),1)])]),t("div",Ut,[e[3]||(e[3]=t("div",{class:"card-header"}," Team details ",-1)),t("div",qt,[t("div",Pt,"#"+$(l.value.id),1),i(D,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:c.value,"onUpdate:modelValue":e[0]||(e[0]=d=>c.value=d)},null,8,["modelValue"])]),c.value!==l.value.name?(s(),n("div",zt,[i(I,{onClick:e[1]||(e[1]=d=>r())},{default:g(()=>e[2]||(e[2]=[y("Update")])),_:1})])):w("",!0)]),t("div",Ft,[i(Z,{loading:m.value},null,8,["loading"]),e[6]||(e[6]=t("div",{class:"card-header"}," Team members ",-1)),o.value.data&&o.value.data.length>0?(s(),n("div",Et,[(s(!0),n(S,null,A(o.value.data,d=>(s(),n("div",Kt,[i(N(V),{href:a.route("admin.user",d.user_id),class:"border-b p-2 mr-2 flex-grow flex justify-between items-center hover:no-underline"},{default:g(()=>{var j;return[t("div",null,$((j=d.user)==null?void 0:j.name),1),t("div",Ot,[d.invitation_sent_at?(s(),n("div",Wt," Invited: "+$(N(P).utc(d.invitation_sent_at).fromNow()),1)):w("",!0),e[4]||(e[4]=t("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor",class:"size-6"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m8.25 4.5 7.5 7.5-7.5 7.5"})],-1))])]}),_:2},1032,["href"]),t("div",Lt,[i(Y,{onClick:j=>x(d),class:"mr-2"},{default:g(()=>[t("div",null,[d.invitation_sent_at?(s(),n("div",Rt,"Resend Invite")):m.value?(s(),n("div",Yt,"Sending..")):(s(),n("div",Zt,"Send Invite"))])]),_:2},1032,["onClick"]),i(J,{onClick:q(j=>b(d),["prevent"])},{default:g(()=>e[5]||(e[5]=[t("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor",class:"h-3 font-bold"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M6 18 18 6M6 6l12 12"})],-1)])),_:2},1032,["onClick"])])]))),256))])):w("",!0),t("div",Gt,[t("div",Ht,[i(B,{onSetDataPage:u,"pagination-data":o.value},null,8,["pagination-data"])])])]),t("div",Jt,[e[7]||(e[7]=t("div",{class:"card-header"}," Add user to team ",-1)),i(bt,{teamId:v.id,onCreateNewTeamUser:C},null,8,["teamId"])]),t("div",Qt,[i(Me,{teamId:v.id,teamName:l.value.name},null,8,["teamId","teamName"])]),t("div",Xt,[i(pt,{teamId:v.id,teamName:l.value.name},null,8,["teamId","teamName"])]),e[8]||(e[8]=t("div",{class:"card"},[t("div",{class:"text-sm pb-2 text-gray-500"},"Voucher sets created by team"),y(" - paginated ")],-1)),e[9]||(e[9]=t("div",{class:"card"},[t("div",{class:"text-sm pb-2 text-gray-500"},"Voucher sets allocated to team"),y(" - paginated ")],-1)),l.value.id?(s(),n("div",ea,[i(Dt,{team:l.value},null,8,["team"])])):w("",!0),e[10]||(e[10]=t("div",{class:"p-32"},null,-1))]),_:1})],64))}};export{ga as default};