import{d as F,e as _,f as S,h as D}from"./index-z2PUUjiU.js";import{d as $,r as s,E as l}from"./.pnpm-DD7z4_75.js";const E=$("fileListStore",()=>{const a=s(!1),t=s({surl:"",url:"",pwd:"",dir:"/",password:""}),o=s(null),L=()=>{const e=t.value.dir.split("/");e.pop();const n=e.join("/");return n===""?"/":n},r=s({sign:"",timestamp:0}),h=async()=>{try{a.value=!0;const e=await D({surl:t.value.surl,uk:i.value.uk,shareid:i.value.shareid,password:t.value.password});r.value.sign=e.data.sign,r.value.timestamp=e.data.timestamp,l.success("获取签名成功")}finally{a.value=!1}},m=async()=>{Date.now()/1e3-r.value.timestamp>250?(l.info("获取签名中"),await h()):l.success("签名未过期,无需更新")},k=async()=>{if(!(!o.value||!await o.value.validate())){if(t.value.surl==="")return l.error("获取链接surl失败");try{u.value=[],a.value=!0;const e=await F(t.value);i.value=e.data,t.value.dir!=="/"&&i.value.list.unshift({category:-1,fs_id:0,isdir:1,local_ctime:0,local_mtime:0,server_ctime:0,server_mtime:0,size:0,md5:"",path:L(),server_filename:"..",dlink:""}),l.success("获取文件列表成功")}finally{a.value=!1,await m()}}},i=s({uk:0,shareid:0,randsk:"",list:[]}),u=s([]),f=s([]),y=async(e,n=!1)=>{const w=e?[e]:u.value.filter(d=>d.isdir!==1).map(d=>d.fs_id);e===void 0&&w.length!==u.value.length&&l.error("文件夹不会被解析!"),await m();let v;try{a.value=!0,v=await _({uk:i.value.uk,shareid:i.value.shareid,randsk:i.value.randsk,fs_ids:w,sign:r.value.sign,timestamp:r.value.timestamp,password:t.value.password}),n||(f.value=v.data),l.success("解析成功"),r.value.timestamp=0}finally{a.value=!1,await p()}if(n)return v.data},g=s({group_name:"",count:0,size:0}),c=s(!1),p=async()=>{try{a.value=!0;const e=await S();g.value=e.data,c.value=!1}catch{c.value=!0}finally{a.value=!1}};return{pending:a,fileList:i,getFileList:k,getFileListForm:t,getFileListFormRef:o,selectedRows:u,downloadLinks:f,getDownloadLinks:y,limitForm:g,getLimit:p,hitLimit:c}});export{E as u};
