import{_ as D}from"./AuthenticatedLayout-Ddc8y2KE.js";import{c as C,g as B,r as F,o as N,a as c,b as d,d as l,u as x,w as m,F as P,Z as $,e as t,t as o,f as r,i as S,h as _}from"./app-De6ADG2Z.js";import{_ as j}from"./AdminTopNavigation-CiIcEXyl.js";import{S as E}from"./sweetalert2.all-CiIoo-le.js";import{d as f}from"./dayjs.min-8HiHFi2H.js";import{r as V}from"./relativeTime-CrOw5TTE.js";import{P as z}from"./PrimaryButton-7kF1Zoeu.js";import"./ApplicationLogo-CmYXJ9Tu.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";var A={exports:{}};(function(h,p){(function(e,i){h.exports=i()})(C,function(){var e={LTS:"h:mm:ss A",LT:"h:mm A",L:"MM/DD/YYYY",LL:"MMMM D, YYYY",LLL:"MMMM D, YYYY h:mm A",LLLL:"dddd, MMMM D, YYYY h:mm A"};return function(i,v,k){var s=v.prototype,a=s.format;k.en.formats=e,s.format=function(n){n===void 0&&(n="YYYY-MM-DDTHH:mm:ssZ");var M=this.$locale().formats,L=function(b,Y){return b.replace(/(\[[^\]]+])|(LTS?|l{1,4}|L{1,4})/g,function(ie,y,u){var g=u&&u.toUpperCase();return y||Y[u]||e[u]||Y[g].replace(/(\[[^\]]+])|(MMMM|MM|DD|dddd)/g,function(re,T,w){return T||w.slice(1)})})}(n,M===void 0?{}:M);return a.call(this,L)}}})})(A);var I=A.exports;const H=B(I),R={class:"card"},Z={class:"card"},q=t("div",{class:"card-header"}," API Access Token details ",-1),G={class:"my-1"},U=t("span",{class:"font-bold"}," Name: ",-1),J={key:0,class:"my-1"},K=t("span",{class:"font-bold"}," Assigned To: ",-1),O={class:"my-1"},Q=t("span",{class:"font-bold"}," Created: ",-1),W={key:1,class:"my-1"},X=t("span",{class:"font-bold"}," Last used: ",-1),ee={key:2,class:"my-1"},te=t("span",{class:"font-bold"}," Expires: ",-1),se={class:"card"},oe=t("div",{class:"card-header"}," Abilities ",-1),ae={key:0},ne={class:"card"},ve={__name:"APIAccessToken",props:{id:{required:!0,type:Number}},setup(h){const p=h,e=F({});N(()=>{v()});function i(s){return f.extend(V),f.extend(H),f(s).fromNow()+" ("+f(s).format("LLL")+")"}function v(){axios.get("/admin/user-personal-access-tokens/"+p.id+"?cached=false&relations=user").then(s=>{e.value=s.data.data}).catch(s=>{console.log(s)})}function k(){E.fire({title:"Are you sure you want to delete this token?",text:"This action cannot be undone, and the user will no longer be able to use this token. Please confirm if you wish to proceed.",icon:"warning",confirmButtonColor:"#3085d6",confirmButtonText:"Revoke this token",showCancelButton:!0}).then(s=>{s.isConfirmed&&axios.delete("/admin/user-personal-access-tokens/"+p.id).then(a=>{window.location.href=route("admin.api-access-tokens")}).catch(a=>{console.log(a)})})}return(s,a)=>(c(),d(P,null,[l(x($),{title:"API Access Token"}),l(D,null,{header:m(()=>[l(j)]),default:m(()=>[t("div",R,[t("h2",null,o(e.value.name)+" (#"+o(e.value.id)+") ",1)]),t("div",Z,[q,t("div",G,[U,r(" "+o(e.value.name),1)]),e.value.tokenable_id?(c(),d("div",J,[K,l(x(S),{href:s.route("admin.user",{id:e.value.tokenable_id})},{default:m(()=>{var n;return[r(o((n=e.value.user)==null?void 0:n.name),1)]}),_:1},8,["href"])])):_("",!0),t("div",O,[Q,r(" "+o(i(e.value.created_at)),1)]),e.value.last_used_at?(c(),d("div",W,[X,r(" "+o(i(e.value.last_used_at)),1)])):_("",!0),e.value.expires_at?(c(),d("div",ee,[te,r(" "+o(i(e.value.expires_at)),1)])):_("",!0)]),t("div",se,[oe,e.value.abilities&&e.value.abilities.length?(c(),d("div",ae,o(e.value.abilities.join(", ")),1)):_("",!0)]),t("div",ne,[l(z,{onClick:a[0]||(a[0]=n=>k())},{default:m(()=>[r(" Revoke this token ")]),_:1})])]),_:1})],64))}};export{ve as default};