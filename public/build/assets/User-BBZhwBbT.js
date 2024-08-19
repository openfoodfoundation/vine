import{r as _,Q as T,e as V,o as s,c as o,d as r,u as v,w as h,F as c,Z as $,b as e,t as u,h as p,a as m,g as N,n as P,j as w,k as S,i as D,l as M,v as j}from"./app-CEyIY_lD.js";import{_ as F}from"./AuthenticatedLayout-Bs5yhaUb.js";import{_ as q}from"./AdminTopNavigation-COfam46o.js";import{S as E}from"./sweetalert2.all-BqECPsVd.js";import{P as z}from"./PaginatorComponent-DxmFMRdB.js";import{_ as G}from"./AdminUserDetailsComponent-CNYxbVmC.js";import{P as L}from"./PrimaryButton-D2JqzYJJ.js";import{_ as Q,a as Z}from"./InputLabel-Dpf4rho3.js";import"./ApplicationLogo-APfCkp9S.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./SecondaryButton-gJqD25JM.js";const H={class:"card"},I={class:"card"},J=e("div",{class:"card-header"}," User details ",-1),K={class:"card"},O=e("div",{class:"card-header"}," User teams ",-1),R={key:0},W={key:0},X={key:0,class:"text-xs text-red-500"},Y={class:""},ee={class:"flex justify-end items-center mt-4"},te={class:"w-full lg:w-1/3"},se={class:"card"},ae=e("div",{class:"card-header"}," User Personal Access Tokens (PATs)) ",-1),oe={key:0},ie={class:"border-b py-2"},ne={class:"list-item ml-8"},le={key:0},re={class:"ml-8 text-xs"},de={key:1},ce={class:"card"},ue=e("div",{class:"card-header"}," Create Personal Access Token (No Edit) ",-1),me={key:0},_e={class:"pb-4"},ve={class:"grid grid-cols-1 md:grid-cols-4"},he=["for"],pe=["id","value"],fe={class:"flex items-center justify-end mt-4"},Te={__name:"User",props:{id:{required:!0,type:Number}},setup(A){const k=A,U=_(5),n=_({name:"",token_abilities:[]}),g=T().props.personalAccessTokenAbilities,d=_({}),l=_({});V(()=>{b(),x()});function C(){n.value.user_id=l.value.id,axios.post("/admin/user-personal-access-tokens",n.value).then(a=>{let i=a.data.data.token;E.fire({title:"Personal access token issued!",html:"Please note that the token will be displayed only once. Make sure to save it securely. </br> <b>"+i+"</b>",icon:"warning",confirmButtonColor:"#3085d6",confirmButtonText:"Got it"}).then(t=>{n.value={name:"",token_abilities:[]},b()})}).catch(a=>{console.log(a)})}function b(){axios.get("/admin/users/"+k.id+"?cached=false&relations=currentTeam").then(a=>{l.value=a.data.data}).catch(a=>{console.log(a)})}function x(a=1){axios.get("/admin/team-users?cached=false&page="+a+"&where[]=user_id,"+k.id+"&relations=team&limit="+U.value+"&orderBy=id,desc").then(i=>{d.value=i.data.data}).catch(i=>{console.log(i)})}function B(a){x(a)}function y(a){return a.replaceAll("-"," ")}return(a,i)=>(s(),o(c,null,[r(v($),{title:"Users"}),r(F,null,{header:h(()=>[r(q)]),default:h(()=>[e("div",H,[e("h2",null,u(l.value.name),1)]),e("div",I,[J,r(G,{user:l.value},null,8,["user"])]),e("div",K,[O,d.value.data&&d.value.data.length>0?(s(),o("div",R,[(s(!0),o(c,null,p(d.value.data,t=>(s(),S(v(D),{href:a.route("admin.team",t.team_id),class:"hover:no-underline hover:opacity-75"},{default:h(()=>[e("div",{class:P({"border-b p-2":d.value.data.length>1})},[t.team?(s(),o("div",W,[t.team_id===l.value.current_team_id?(s(),o("div",X,"*Current team ")):m("",!0),e("div",Y,u(t.team.name),1)])):m("",!0)],2)]),_:2},1032,["href"]))),256))])):m("",!0),e("div",ee,[e("div",te,[r(z,{onSetDataPage:B,"pagination-data":d.value},null,8,["pagination-data"])])])]),e("div",se,[ae,l.value.tokens&&l.value.tokens.length?(s(),o("div",oe,[(s(!0),o(c,null,p(l.value.tokens,t=>(s(),o("div",ie,[e("div",ne,u(t.name),1),t.abilities&&t.abilities.length?(s(),o("div",le,[(s(!0),o(c,null,p(t.abilities,f=>(s(),o("div",re," - "+u(y(f)),1))),256))])):m("",!0)]))),256))])):(s(),o("div",de,"User does not have PATs."))]),e("div",ce,[ue,v(g).length?(s(),o("div",me,[e("div",_e,[r(Q,{for:"name",value:"PAT name"}),r(Z,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:n.value.name,"onUpdate:modelValue":i[0]||(i[0]=t=>n.value.name=t),required:""},null,8,["modelValue"])]),e("div",ve,[(s(!0),o(c,null,p(v(g),t=>(s(),o("div",null,[e("label",{for:t,class:"cursor-pointer"},[M(e("input",{type:"checkbox",id:t,class:"mr-4",value:t,"onUpdate:modelValue":i[1]||(i[1]=f=>n.value.token_abilities=f)},null,8,pe),[[j,n.value.token_abilities]]),w(" "+u(y(t)),1)],8,he)]))),256))]),e("div",fe,[r(L,{onClick:i[2]||(i[2]=N(t=>C(),["prevent"])),class:P(["ms-4",{"opacity-25":!n.value.name}]),desabled:!n.value.name},{default:h(()=>[w(" Submit ")]),_:1},8,["class","desabled"])])])):m("",!0)])]),_:1})],64))}};export{Te as default};