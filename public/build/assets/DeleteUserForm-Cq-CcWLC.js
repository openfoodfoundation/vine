import{q as b,o as k,x as C,y as D,a as g,k as B,d as o,w as l,p as f,G as y,e as s,H as p,n as h,E,h as V,I as $,r as v,T as U,b as S,J as T,f as w,u as c,l as A}from"./app-VZyAvj5K.js";import{D as x}from"./DangerButton-BzrES3GP.js";import{_ as N}from"./InputError-BXOg14_7.js";import{_ as I}from"./InputLabel-BjudjgZy.js";import{_ as M}from"./SecondaryButton-D6T9TBQU.js";import{_ as O}from"./TextInput-BCLP2L3X.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";const P={class:"fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50","scroll-region":""},W={__name:"Modal",props:{show:{type:Boolean,default:!1},maxWidth:{type:String,default:"2xl"},closeable:{type:Boolean,default:!0}},emits:["close"],setup(n,{emit:r}){const a=n,t=r;b(()=>a.show,()=>{a.show?document.body.style.overflow="hidden":document.body.style.overflow=null});const m=()=>{a.closeable&&t("close")},d=u=>{u.key==="Escape"&&a.show&&m()};k(()=>document.addEventListener("keydown",d)),C(()=>{document.removeEventListener("keydown",d),document.body.style.overflow=null});const i=D(()=>({sm:"sm:max-w-sm",md:"sm:max-w-md",lg:"sm:max-w-lg",xl:"sm:max-w-xl","2xl":"sm:max-w-2xl"})[a.maxWidth]);return(u,e)=>(g(),B($,{to:"body"},[o(p,{"leave-active-class":"duration-200"},{default:l(()=>[f(s("div",P,[o(p,{"enter-active-class":"ease-out duration-300","enter-from-class":"opacity-0","enter-to-class":"opacity-100","leave-active-class":"ease-in duration-200","leave-from-class":"opacity-100","leave-to-class":"opacity-0"},{default:l(()=>[f(s("div",{class:"fixed inset-0 transform transition-all",onClick:m},e[0]||(e[0]=[s("div",{class:"absolute inset-0 bg-gray-500 opacity-75"},null,-1)]),512),[[y,n.show]])]),_:1}),o(p,{"enter-active-class":"ease-out duration-300","enter-from-class":"opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95","enter-to-class":"opacity-100 translate-y-0 sm:scale-100","leave-active-class":"ease-in duration-200","leave-from-class":"opacity-100 translate-y-0 sm:scale-100","leave-to-class":"opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"},{default:l(()=>[f(s("div",{class:h(["mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto",i.value])},[n.show?E(u.$slots,"default",{key:0}):V("",!0)],2),[[y,n.show]])]),_:3})],512),[[y,n.show]])]),_:3})]))}},z={class:"space-y-6"},F={class:"p-6"},K={class:"mt-6"},L={class:"mt-6 flex justify-end"},X={__name:"DeleteUserForm",setup(n){const r=v(!1),a=v(null),t=U({password:""}),m=()=>{r.value=!0,T(()=>a.value.focus())},d=()=>{t.delete(route("profile.destroy"),{preserveScroll:!0,onSuccess:()=>i(),onError:()=>a.value.focus(),onFinish:()=>t.reset()})},i=()=>{r.value=!1,t.reset()};return(u,e)=>(g(),S("section",z,[e[6]||(e[6]=s("header",null,[s("h2",{class:"text-lg font-medium text-gray-900"},"Delete Account"),s("p",{class:"mt-1 text-sm text-gray-600"}," Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain. ")],-1)),o(x,{onClick:m},{default:l(()=>e[1]||(e[1]=[w("Delete Account")])),_:1}),o(W,{show:r.value,onClose:i},{default:l(()=>[s("div",F,[e[4]||(e[4]=s("h2",{class:"text-lg font-medium text-gray-900"}," Are you sure you want to delete your account? ",-1)),e[5]||(e[5]=s("p",{class:"mt-1 text-sm text-gray-600"}," Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. ",-1)),s("div",K,[o(I,{for:"password",value:"Password",class:"sr-only"}),o(O,{id:"password",ref_key:"passwordInput",ref:a,modelValue:c(t).password,"onUpdate:modelValue":e[0]||(e[0]=_=>c(t).password=_),type:"password",class:"mt-1 block w-3/4",placeholder:"Password",onKeyup:A(d,["enter"])},null,8,["modelValue"]),o(N,{message:c(t).errors.password,class:"mt-2"},null,8,["message"])]),s("div",L,[o(M,{onClick:i},{default:l(()=>e[2]||(e[2]=[w(" Cancel ")])),_:1}),o(x,{class:h(["ms-3",{"opacity-25":c(t).processing}]),disabled:c(t).processing,onClick:d},{default:l(()=>e[3]||(e[3]=[w(" Delete Account ")])),_:1},8,["class","disabled"])])])]),_:1},8,["show"])]))}};export{X as default};