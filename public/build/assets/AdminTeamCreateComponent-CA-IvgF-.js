import{r as u,o as _,a as l,b as n,e as o,f as i,d,p as b,v as S,F as w,j as x,t as C,w as V,n as g,m as k}from"./app-CZIcKt2y.js";import{_ as N}from"./TextInput-B7vHmZHl.js";import{P as j}from"./PrimaryButton-DvVpI_7K.js";import{S as B}from"./AuthenticatedLayout-C2hWJ0Ka.js";const T={class:"flex justify-start items-center mt-4"},h={class:"w-full font-bold",for:"name"},M={class:"flex justify-start items-center mt-4"},$={class:"w-full font-bold",for:"country"},D=["value"],F={class:"flex items-center justify-end mt-4"},E={__name:"AdminTeamCreateComponent",props:{searchStr:{default:null}},emits:["teamCreated"],setup(c,{emit:f}){const r=c,m=u({}),t=u({name:"",country_id:""}),v=f;_(()=>{r.searchStr!==null&&(t.value.name=r.searchStr),y()});function p(){axios.post("/admin/teams",t.value).then(a=>{B.fire({title:"Success!",icon:"success",timer:1e3}).then(()=>{let e=a.data.data;v("teamCreated",e),e.value={}})}).catch(a=>{console.log(a)})}function y(){axios.get("/countries?limit=250").then(a=>{m.value=a.data.data}).catch(a=>{console.log(a)})}return(a,e)=>(l(),n("form",{onSubmit:e[3]||(e[3]=k(s=>p(),["prevent"]))},[o("div",null,[o("div",T,[o("label",h,[e[4]||(e[4]=i(" Team Name: ")),d(N,{id:"name",modelValue:t.value.name,"onUpdate:modelValue":e[0]||(e[0]=s=>t.value.name=s),class:"mt-1 block w-full font-normal",type:"text"},null,8,["modelValue"])])]),o("div",M,[o("label",$,[e[6]||(e[6]=i(" Country: ")),b(o("select",{id:"country","onUpdate:modelValue":e[1]||(e[1]=s=>t.value.country_id=s),class:"mt-1 block w-full font-normal"},[e[5]||(e[5]=o("option",{value:""},"Select a country",-1)),(l(!0),n(w,null,x(m.value.data,s=>(l(),n("option",{key:s.id,value:s.id},C(s.name),9,D))),128))],512),[[S,t.value.country_id]])])])]),o("div",F,[d(j,{class:g([{"opacity-25":!t.value.name||!t.value.country_id},"ms-4 hover:cursor-pointer"]),disabled:!t.value.name||!t.value.country_id,onClick:e[2]||(e[2]=()=>{})},{default:V(()=>e[7]||(e[7]=[i(" Submit ")])),_:1},8,["class","disabled"])])],32))}};export{E as _};