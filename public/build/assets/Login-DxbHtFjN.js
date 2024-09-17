import{T as _,a as l,k as n,w as i,d as o,u as t,Z as b,b as y,t as k,h as d,e as a,i as x,f as u,n as V,m as v}from"./app-CsvKUCVF.js";import{_ as B}from"./Checkbox-05bqmkD7.js";import{_ as P}from"./GuestLayout-DrNmNMPD.js";import{_ as f}from"./InputError-DxWjyap5.js";import{_ as c}from"./InputLabel-BVjOt5oO.js";import{P as $}from"./PrimaryButton-CrY-NrTG.js";import{_ as p}from"./TextInput-YzWOwcQi.js";import"./ApplicationLogo-Cox3Zk07.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";const h={key:0,class:"mb-4 font-medium text-sm text-green-600"},N={class:"mt-4"},q={class:"block mt-4"},C={class:"flex items-center"},L={class:"flex items-center justify-end mt-4"},M={__name:"Login",props:{canResetPassword:{type:Boolean},status:{type:String}},setup(m){const e=_({email:"",password:"",remember:!1}),g=()=>{e.post(route("login"),{onFinish:()=>e.reset("password")})};return(w,s)=>(l(),n(P,null,{default:i(()=>[o(t(b),{title:"Log in"}),m.status?(l(),y("div",h,k(m.status),1)):d("",!0),a("form",{onSubmit:v(g,["prevent"])},[a("div",null,[o(c,{for:"email",value:"Email"}),o(p,{id:"email",type:"email",class:"mt-1 block w-full",modelValue:t(e).email,"onUpdate:modelValue":s[0]||(s[0]=r=>t(e).email=r),required:"",autofocus:"",autocomplete:"username"},null,8,["modelValue"]),o(f,{class:"mt-2",message:t(e).errors.email},null,8,["message"])]),a("div",N,[o(c,{for:"password",value:"Password"}),o(p,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:t(e).password,"onUpdate:modelValue":s[1]||(s[1]=r=>t(e).password=r),required:"",autocomplete:"current-password"},null,8,["modelValue"]),o(f,{class:"mt-2",message:t(e).errors.password},null,8,["message"])]),a("div",q,[a("label",C,[o(B,{name:"remember",checked:t(e).remember,"onUpdate:checked":s[2]||(s[2]=r=>t(e).remember=r)},null,8,["checked"]),s[3]||(s[3]=a("span",{class:"ms-2 text-sm text-gray-600"},"Remember me",-1))])]),a("div",L,[m.canResetPassword?(l(),n(t(x),{key:0,href:w.route("password.request"),class:"underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"},{default:i(()=>s[4]||(s[4]=[u(" Forgot your password? ")])),_:1},8,["href"])):d("",!0),o($,{class:V(["ms-4",{"opacity-25":t(e).processing}]),disabled:t(e).processing},{default:i(()=>s[5]||(s[5]=[u(" Log in ")])),_:1},8,["class","disabled"])])],32)]),_:1}))}};export{M as default};