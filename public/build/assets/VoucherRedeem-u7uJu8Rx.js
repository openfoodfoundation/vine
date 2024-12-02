import{r as _,o as V,p as F,c as s,a as p,u as x,w as $,F as b,b as i,Z as C,d as e,e as f,t as n,f as u,l as T,q as B,n as P,g as A}from"./app-DolNWhzr.js";import{_ as I,S as m}from"./AuthenticatedLayout-DuLcXyKk.js";import{d as h}from"./dayjs.min-ilzO22t1.js";import{r as N}from"./relativeTime-BTl_QCHW.js";import{u as j}from"./utc-CWodzHWT.js";import"./ApplicationLogo-B917rUN3.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./PrimaryButton-BtqGl7rR.js";const D={class:"card"},S={class:"title text-2xl"},L={class:"uppercase"},q={class:"my-4"},M={key:0,class:"title text-green text-xl text-green-500"},O={key:1,class:"title"},z={key:2,class:"title text-red text-lg text-red-500"},E={class:"text-2xl font-bold mt-12"},H={key:3,class:"mt-12"},U={key:0},Z={class:"my-4"},G={key:0,class:"my-2 text-red-500"},J={key:0,class:"flex justify-between items-center"},K={class:"w-1/2 pr-2"},Q={class:"w-1/2 pl-2"},W={key:1,class:"grid grid-cols-1 md:grid-cols-2 gap-2"},X={class:""},Y={class:""},ee={key:4,class:"mt-8"},te={key:5,class:"mt-12 text-left"},oe={class:"title"},se={class:"flex justify-between items-center py-2 border-b"},ie={class:"text-lg"},re={class:"text-center"},le={class:"text-xs"},xe={__name:"VoucherRedeem",props:{voucher:{type:Object,required:!0}},setup(r){h.extend(N),h.extend(j);const d=r,c=_(!1),a=_(0),v=_(!1);V(()=>{});function y(){a.value=(parseInt(d.voucher.voucher_value_remaining)/100).toFixed(2),c.value=!0}function w(){c.value=!1}function k(){c.value=!1,m.fire({title:"Redeem all $"+(d.voucher.voucher_value_remaining/100).toFixed(2)+"?",html:"<p>This will fully redeem this voucher.</p>",icon:"warning",showCancelButton:!0,confirmButtonText:"Redeem!"}).then(l=>{l.value&&g(d.voucher.voucher_value_remaining.toFixed(0))})}function R(){m.fire({title:"Redeem $"+a.value+"?",html:"<p>This will partially redeem this voucher.</p>",icon:"warning",showCancelButton:!0,confirmButtonText:"Redeem!"}).then(l=>{l.value&&g((a.value*100).toFixed(0))})}function g(l){let t={voucher_id:d.voucher.id,voucher_set_id:d.voucher.voucher_set_id,amount:l};axios.post("/voucher-redemptions",t).then(o=>{m.fire({icon:"success",title:"Redeemed.",text:o.data.meta.message}),c.value=!1,setTimeout(ne=>{window.location.reload()},1e3)}).catch(o=>{m.fire({icon:"error",title:"Oops!",text:o.response.data.meta.message})})}return F(a,l=>{v.value=l>0&&parseInt((l*100).toFixed(0))<=parseInt(d.voucher.voucher_value_remaining.toFixed(0))}),(l,t)=>(i(),s(b,null,[p(x(C),{title:"Voucher redeem"}),p(I,null,{default:$(()=>[e("div",D,[e("div",S,[t[5]||(t[5]=f(" Redeem Voucher ")),e("span",L,n(r.voucher.voucher_short_code),1)]),e("div",q,[r.voucher.voucher_value_remaining>0?(i(),s("div",M,"Voucher is Valid")):(i(),s("div",O,"Voucher is Fully Redeemed!")),r.voucher.is_test?(i(),s("div",z," This is a test voucher. ")):u("",!0),e("div",null,[e("div",E," $"+n((r.voucher.voucher_value_remaining/100).toFixed(2))+" remaining ",1),e("div",null," of $"+n((r.voucher.voucher_value_original/100).toFixed(2))+" original value ",1)]),r.voucher.voucher_value_remaining>0?(i(),s("div",H,[c.value?(i(),s("div",U,[e("div",Z,[t[6]||(t[6]=f(" How much should be redeemed? ")),T(e("input",{inputmode:"decimal",pattern:"[0-9]*",type:"text",step:"0.01","onUpdate:modelValue":t[0]||(t[0]=o=>a.value=o),class:P(["w-full text-center text-xl rounded p-8 border-2 focus:outline-none",{"border-green-500":v.value,"border-red-500":!v.value}]),min:"0.01"},null,2),[[B,a.value,void 0,{number:!0}]]),v.value?u("",!0):(i(),s("div",G," Invalid redemption amount. "))]),v.value?(i(),s("div",J,[e("div",K,[e("button",{class:"w-full p-8 font-bold text-2xl rounded border bg-gray-300",onClick:t[1]||(t[1]=o=>w())}," Cancel ")]),e("div",Q,[e("button",{class:"w-full p-8 font-bold text-2xl rounded border bg-gray-300",onClick:t[2]||(t[2]=o=>R())}," Redeem ")])])):u("",!0)])):(i(),s("div",W,[e("div",X,[e("button",{class:"w-full p-8 font-bold text-2xl rounded border bg-gray-300",onClick:t[3]||(t[3]=o=>y())}," Redeem PART ")]),e("div",Y,[e("button",{class:"w-full p-8 font-bold text-2xl rounded border bg-gray-300",onClick:t[4]||(t[4]=o=>k())}," Redeem ALL ")])]))])):u("",!0),r.voucher.voucher_redemptions?u("",!0):(i(),s("div",ee,t[7]||(t[7]=[e("button",{class:"w-full p-2 rounded border"}," See Redemptions ",-1)]))),r.voucher.voucher_redemptions?(i(),s("div",te,[e("div",oe," Redemptions ("+n(r.voucher.voucher_redemptions.length)+") ",1),e("div",null,[(i(!0),s(b,null,A(r.voucher.voucher_redemptions,o=>(i(),s("div",se,[e("div",null,[e("div",ie," $"+n((o.redeemed_amount/100).toFixed(2)),1)]),e("div",re,[f(n(x(h).utc(o.created_at).fromNow())+" ",1),e("div",le," ("+n(x(h)(o.created_at))+") ",1)])]))),256))])])):u("",!0)])])]),_:1})],64))}};export{xe as default};