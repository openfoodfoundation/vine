import{r as u,e as d,o as f,c as p,b as n,d as s,w as v,j as _,n as S,g as x}from"./app-D7vdOgIt.js";import{_ as b,a as h}from"./InputLabel-BCWRcgez.js";import{P as w}from"./PrimaryButton-9z9RrTGZ.js";import{S as C}from"./AdminTopNavigation-Fmss29Es.js";const V={class:"flex items-center justify-end mt-4"},k={__name:"AdminTeamCreateComponent",props:{searchStr:{default:null}},emits:["teamCreated"],setup(l,{emit:r}){const m=l,a=u({name:""}),i=r;d(()=>{m.searchStr!==null&&(a.value.name=m.searchStr)});function c(){axios.post("/admin/teams",a.value).then(t=>{C.fire({title:"Success!",icon:"success",timer:1e3}).then(()=>{let e=t.data.data;i("teamCreated",e),e.value={}})}).catch(t=>{console.log(t)})}return(t,e)=>(f(),p("form",{onSubmit:e[1]||(e[1]=x(o=>c(),["prevent"]))},[n("div",null,[s(b,{for:"name",value:"Name"}),s(h,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:a.value.name,"onUpdate:modelValue":e[0]||(e[0]=o=>a.value.name=o),required:""},null,8,["modelValue"])]),n("div",V,[s(w,{class:S(["ms-4",{"opacity-25":!a.value.name}]),disabled:!a.value.name},{default:v(()=>[_(" Submit ")]),_:1},8,["class","disabled"])])],32))}};export{k as _};