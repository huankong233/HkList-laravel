@extends('layouts.main')

@section('title', '前台解析中心')

@section('template')
    <el-dialog
            v-model="Announce.switch"
            title="公告"
            width="90%"
    >
        <span v-html="Announce.message"></span>
    </el-dialog>

    <el-dialog
            v-model="configAria2FormVisible"
            title="Aria2配置"
            width="90%"
    >
        <el-form
                ref="configAria2FormRef"
                v-bind:model="configAria2Form"
                v-bind:rules="configAria2FormRule"
                label-width="200px"
        >
            <el-form-item label="Aria2 服务器地址" prop="host">
                <el-input v-model="configAria2Form.host"></el-input>
            </el-form-item>
            <el-form-item label="Aria2 端口号" prop="port">
                <el-input v-model="configAria2Form.port"></el-input>
            </el-form-item>
            <el-form-item label="Aria2 下载密钥" prop="secret">
                <el-input v-model="configAria2Form.secret"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary"
                           v-on:click="configAria2(configAria2FormRef)"
                >
                    保存
                </el-button>
            </el-form-item>
        </el-form>
    </el-dialog>

    <el-dialog v-model="downloadDialogVisible" title="解析任务列表" width="80%">
        <el-space>
            <el-text class="mx-1">当前的UA :</el-text>
            <el-link type="danger" @click="copy(user_agent,'已复制UA')">@{{ user_agent }}</el-link>
            <el-button type="primary" v-bind:disabled="selectDownloadFiles.length <= 0" @click="sendDownloadFiles">
                批量下载
            </el-button>
            <el-button type="primary" @click="openDownloadListDialog">下载配置</el-button>
        </el-space>
        <el-table
                border
                show-overflow-tooltip
                class="table"
                :data="dlinkList"
                @selection-change="selectDownloadFilesChange">
            <el-table-column type="selection" width="40"></el-table-column>
            <el-table-column prop="server_filename" label="文件名"></el-table-column>
            <el-table-column prop="dlink" label="下载链接"></el-table-column>
            <el-table-column label="操作">
                <template #default="scope">
                    <el-button
                            type="primary"
                            size="small"
                            @click="copy(scope.row.dlink,'已将链接复制到粘贴板内')"
                    >
                        复制链接
                    </el-button>
                    <el-button
                            type="primary"
                            size="small"
                            @click="sendDownloadFile(scope.row.dlink,scope.row.server_filename)"
                    >
                        发送Aria2
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
    </el-dialog>

    <el-card
            v-loading="getFileListForm.pending"
    >
        <h2>前台解析中心 | {{ config("app.name") }}</h2>
        @if(\App\Http\Controllers\UserController::getRandomCookie() === null)
            <el-alert title="当前中转账号不足" type="error"></el-alert>
        @else
            <el-alert title="当前中转账号充足" type="success"></el-alert>
        @endif
        @if(config("app.debug") === true)
            <el-alert class="alert" title="当前网站开启了DEBUG模式,非调试请关闭!!!!"
                      type="error"></el-alert>
        @endif
        @if(!Request::secure() && !config("94list.ssl"))
            <el-alert class="alert" title="当前网站未开启SSL,可能出现无法请求Aria2服务器的问题"
                      type="error"></el-alert>
        @endif
        <el-form
                ref="getFileListFormRef"
                v-bind:model="getFileListForm"
                v-bind:rules="getFileListFormRule"
                label-width="100"
                class="form"
        >
            <el-form-item label="链接" prop="url">
                <el-input v-model="getFileListForm.url" @blur="checkLink"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password">
                <el-input v-model="getFileListForm.password"></el-input>
            </el-form-item>
            @if(Auth::check())
                <el-form-item label="指定用户解析" prop="bd_user_id">
                    <el-input v-model="getFileListForm.bd_user_id"></el-input>
                </el-form-item>
            @endif
            <el-form-item label="当前路径" prop="dir">
                <el-input v-model="dir" disabled></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary"
                           v-on:click="getFileListClickEvent(getFileListFormRef)"
                >
                    解析链接
                </el-button>
                <el-button type="primary"
                           v-on:click="freshFileList(getFileListFormRef)"
                >
                    刷新列表
                </el-button>
                <el-button type="primary"
                           v-bind:disabled="selectedRows.length <= 0"
                           v-on:click="downloadFiles"
                >
                    批量解析
                </el-button>
                <el-button type="primary"
                           v-on:click="copyLink(getFileListFormRef)"
                >
                    复制当前地址
                </el-button>
            </el-form-item>
        </el-form>
    </el-card>

    <el-card class="card"
             v-loading="getFileListForm.pending"
    >
        <el-table border
                  stripe
                  ref="fileListTableRef"
                  v-bind:data="list"
                  @row-click="clickRow"
                  @row-dblclick="dblclickRow"
                  @selection-change="clickSelection">
            <el-table-column type="selection" width="40"></el-table-column>
            <el-table-column label="文件名">
                <template #default="scope">
                    <el-space wrap>
                        <img
                                :src="scope.row.isdir == '1' ? '/assets/images/folder.png' : '/assets/images/unknownfile.png'"
                                style="width: 20px; height: 20px;"
                        />
                        @{{ scope.row.server_filename }}
                    </el-space>
                </template>
            </el-table-column>
            <el-table-column label="修改时间">
                <template #default="scope">
                    @{{ formatTimestamp(scope.row.server_mtime) }}
                </template>
            </el-table-column>
            <el-table-column label="大小">
                <template #default="scope">
                    @{{ formatBytes(scope.row.size) }}
                </template>
            </el-table-column>
        </el-table>
    </el-card>
@endsection

@section('scripts')
    <script>
        const {createApp, ref, onMounted} = Vue
        const {ElMessage} = ElementPlus

        const app = createApp({
                setup() {
                    const Announce = ref({
                        switch: false,
                        message: `{!!config("94list.announce")!!}`
                    })

                    @if($fetchOnIn)
                    onMounted(() => {
                        ElMessage.success("已收到链接,即将自动解析")
                        setTimeout(() => getFileListClickEvent(getFileListFormRef.value), 1000)
                    })
                    @else
                    // 淡入效果
                    setTimeout(() => Announce.value.switch = {{config("94list.announceSwitch")?'true':'false'}}, 300)
                    @endif

                    const getFileListForm = ref({
                        url: "{{$url}}",
                        password: "{{$pwd}}",
                        pending: false,
                        @if(Auth::check())
                        "bd_user_id": null
                        @endif
                    })

                    const getFileListFormRef = ref(null)

                    const getUrlId = (url) => {
                        const fullMatch = url.match(/s\/([a-zA-Z0-9_-]+)/);
                        const passwordMatch = url.match(/\?pwd=([a-zA-Z0-9_-]+)/)
                        if (fullMatch) {
                            return {
                                type: "full",
                                id: fullMatch[1],
                                password: passwordMatch ? passwordMatch[1] : null
                            };
                        }

                        const shortMatch = url.match(/surl=([a-zA-Z0-9_-]+)/);
                        if (shortMatch) {
                            return {
                                type: "short",
                                id: shortMatch[1]
                            };
                        }

                        return false
                    }

                    const urlValidator = (rule, value, callback) => {
                        if (value === '') {
                            return callback(new Error('请先输入需要解析的链接'))
                        }

                        if (getUrlId(value)) {
                            return callback()
                        } else {
                            return callback(new Error('请输入合法的链接'))
                        }
                    }

                    const getFileListFormRule = {
                        url: [{validator: urlValidator, trigger: 'blur'}]
                    }

                    const uk = ref(null)
                    const shareid = ref(null)
                    const randsk = ref(null)
                    const list = ref([])
                    const sign = ref(null)
                    const timestamp = ref(0)
                    const dir = ref("{{$dir}}")

                    const getFileListClickEvent = async (formEl) => {
                        if (!formEl) return
                        if (!await formEl.validate(() => {
                        })) return

                        getFileListForm.value.pending = true

                        // 如果获取列表成功再获取签名
                        if (await getFileList()) await getFileSign()

                        getFileListForm.value.pending = false
                    }

                    // 根据路径生成上一个路径的地址
                    const getPreviousPath = () => {
                        let newArr = dir.value.split("/")
                        newArr.pop()
                        const newPath = newArr.join("/")
                        return newPath === '' ? "/" : newPath
                    }

                    const getFileList = async (server_mtime = 0, refresh = false) => {
                        const fileList = await axios.post("{{relative_route('user.getFileList')}}", {
                            url: getFileListForm.value.url,
                            password: getFileListForm.value.password,
                            dir: dir.value
                        }).catch(error => {
                            const {response: {data: {message}, status}} = error
                            ElMessage.error(status === 400 ? message : '服务器错误')
                        }) ?? "failed"

                        if (fileList === 'failed') return

                        let {data: {message, data}} = fileList
                        ElMessage.success(message)

                        uk.value = data.uk
                        shareid.value = data.shareid
                        randsk.value = data.randsk
                        if (dir.value !== null && dir.value !== '' && dir.value !== '/') {
                            list.value = [{
                                isdir: "1",
                                path: getPreviousPath(),
                                server_filename: "..",
                                size: "0",
                                server_mtime: refresh ? list.value[0].server_mtime : server_mtime
                            }, ...(data.list)]
                        } else {
                            list.value = data.list
                        }

                        return 'success'
                    }

                    const freshFileList = async (formEl) => {
                        if (!formEl) return
                        if (!await formEl.validate(() => {
                        })) return

                        getFileListForm.value.pending = true
                        await getFileList(0, true)
                        await getFileSign()
                        getFileListForm.value.pending = false
                    }

                    const checkLink = () => {
                        list.value = []
                        dir.value = '/'
                        selectedRows.value = []

                        const data = getUrlId(getFileListForm.value.url)
                        if (data.type === 'full') {
                            if (data.id) getFileListForm.value.url = `https://pan.baidu.com/s/${data.id}`
                            if (data.password) {
                                getFileListForm.value.password = data.password
                                ElMessage.success("已自动填写密码")
                            }
                        }
                    }

                    const getFileSign = async () => {
                        const response = await axios.post("{{relative_route('user.getSign')}}", {
                            uk: uk.value,
                            shareid: shareid.value
                        }).catch(error => {
                            const {response: {data: {message}, status}} = error
                            ElMessage.error(status === 400 ? message : '服务器错误')
                        }) ?? "failed"

                        if (response === 'failed') return

                        const {data: {message, data}} = response
                        ElMessage.success(message)

                        sign.value = data.sign
                        timestamp.value = data.timestamp
                    }

                    const formatTimestamp = (timestamp) => {
                        const date = new Date(timestamp * 1000)
                        return date.toLocaleDateString(undefined, {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })
                    }

                    const formatBytes = (bytes, decimals = 2) => {
                        bytes = parseFloat(bytes)
                        if (bytes === 0) return '0 Bytes'
                        const k = 1024
                        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
                        const i = Math.floor(Math.log(bytes) / Math.log(k))
                        return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i]
                    }

                    const downloadFile = async (fs_id) => {
                        const response = await axios.post("{{relative_route('user.downloadFiles')}}", {
                            // 如果fs_id是数组则表示批量下载
                            // 否则就是单个下载手动修改成数组
                            fs_ids: Array.isArray(fs_id) ? fs_id : [fs_id],
                            timestamp: timestamp.value,
                            uk: uk.value,
                            sign: sign.value,
                            randsk: randsk.value,
                            shareid: shareid.value,
                            @if(Auth::check())
                            "bd_user_id": getFileListForm.value["bd_user_id"]
                            @endif
                        }).catch(async error => {
                            const {response: {data: {message}, status}} = error
                            if (message.includes("当前签名已过期")) {
                                ElMessage.error(message)
                                ElMessage.success("自动重新获取中...")
                                await freshFileList()
                            } else {
                                ElMessage.error(status === 400 ? message : '服务器错误')
                            }
                        }) ?? "failed"

                        if (response === 'failed') return

                        const {data: {message, data}} = response
                        ElMessage.success("解析成功")
                        dlinkList.value = data
                        downloadDialogVisible.value = true
                    }

                    const downloadFiles = async () => {
                        getFileListForm.value.pending = true
                        let bad = false
                        selectedRows.value = selectedRows.value.filter(item => {
                            if (bad) return false
                            const bool = item.isdir === '1' || item.isdir === 1
                            if (bool) {
                                bad = true
                                ElMessage.error("请勿勾选文件夹")
                                fileListTableRef.value?.clearSelection()
                            }
                            return !bool
                        })

                        if (bad) {
                            getFileListForm.value.pending = false
                            return
                        }

                        if (selectedRows.value.length <= 0 || selectedRows.value.length >= parseFloat({{config("94list.max_once")}})) {
                            ElMessage.error(`一次请求请不要超过${parseFloat({{config("94list.max_once")}})}个文件`)
                            fileListTableRef.value?.clearSelection()
                            getFileListForm.value.pending = false
                            return
                        }

                        // 收集fids
                        const fs_ids = selectedRows.value.map(item => item.fs_id)
                        await downloadFile(fs_ids)
                        getFileListForm.value.pending = false
                    }

                    const getDir = async (path, server_mtime) => {
                        dir.value = path
                        await getFileList(server_mtime)
                    }

                    const dblclickRow = async (scope) => {
                        getFileListForm.value.pending = true
                        if (scope.isdir === "1" || scope.isdir === 1) {
                            await getDir(scope.path, scope.server_mtime)
                        } else {
                            await downloadFile(scope.fs_id, scope.server_filename)
                        }
                        getFileListForm.value.pending = false
                    }

                    const clickRow = async (scope) => {
                        if (!/Mobi|Android|iPhone/i.test(navigator.userAgent)) return
                        getFileListForm.value.pending = true
                        if (scope.isdir === "1" || scope.isdir === 1) {
                            await getDir(scope.path, scope.server_mtime)
                        } else {
                            await downloadFile(scope.fs_id, scope.server_filename)
                        }
                        getFileListForm.value.pending = false
                    }


                    const fileListTableRef = ref(null)
                    const selectedRows = ref([])

                    const clickSelection = (row) => selectedRows.value = row

                    const downloadDialogVisible = ref(false)

                    const dlinkList = ref([])

                    const user_agent = "{{config("94list.user_agent")}}"

                    const copy = (text, message) => {
                        const textarea = document.createElement("textarea");
                        textarea.value = text;
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand("copy");
                        document.body.removeChild(textarea);

                        if (message) {
                            ElMessage({
                                message: message,
                                type: 'success'
                            });
                        }
                    }

                    const sendDownloadFile = async (dlink, filename) => {
                        const response = await axios.post(`${configAria2Form.value.host}:${configAria2Form.value.port}/jsonrpc`, {
                            jsonrpc: '2.0',
                            id: "{{config("app.name")}}",
                            method: 'aria2.addUri',
                            params: [
                                `token:${configAria2Form.value.secret}`,
                                [dlink],
                                {
                                    'out': filename,
                                    'header': [`User-Agent: ${user_agent}`]
                                }
                            ]
                        }).catch(error => {
                            ElMessage.error('发送失败，请检查控制台输出')
                        }) ?? 'failed'

                        if (response !== 'failed') {
                            ElMessage.success(`已把${filename}任务发送给下载器`)
                        }
                    }

                    const selectDownloadFiles = ref([])
                    const selectDownloadFilesChange = (row) => selectDownloadFiles.value = row
                    const sendDownloadFiles = async () => {
                        ElMessage.error("请确保最大同时下载文件数在5及以下,否则可能下载失败!")
                        ElMessage.error("请确保最大同时下载文件数在5及以下,否则可能下载失败!")
                        ElMessage.error("请确保最大同时下载文件数在5及以下,否则可能下载失败!")
                        await sleep(3000)
                        ElMessage.success("开始下载")
                        selectDownloadFiles.value.forEach(item => sendDownloadFile(item.dlink, item.server_filename))
                    }

                    const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms))

                    const configAria2Form = ref({
                        host: "http://localhost",
                        port: "6800",
                        secret: ""
                    })
                    const configAria2FormRule = {
                        host: [{required: true, message: '请输入Aria2 服务器地址', trigger: 'blur'}],
                        port: [{required: true, message: '请输入Aria2 端口号', trigger: 'blur'}]
                    }
                    const configAria2FormRef = ref(null)
                    const configAria2FormVisible = ref(false)
                    const configAria2 = async (formEl) => {
                        if (!formEl) return
                        if (await formEl.validate(() => {
                        })) {
                            localStorage.setItem('configAria2', JSON.stringify(configAria2Form.value))
                            ElMessage.success("保存成功")
                            configAria2FormVisible.value = false
                        }
                    }

                    onMounted(() => {
                        const config = localStorage.getItem("configAria2")
                        if (config) {
                            configAria2Form.value = JSON.parse(config)
                        }
                    })

                    const openDownloadListDialog = () => configAria2FormVisible.value = true

                    const copyLink = async (formEl) => {
                        if (!formEl) return
                        if (!await formEl.validate(() => {
                        })) return

                        copy(`{{relative_route("user")}}/?url=${getFileListForm.value.url}&pwd=${getFileListForm.value.password}&dir=${dir.value}`, "复制成功")
                    }

                    return {
                        Announce,

                        getFileList,
                        getFileListClickEvent,
                        getFileSign,
                        getFileListForm,
                        getFileListFormRef,
                        getFileListFormRule,

                        freshFileList,
                        checkLink,

                        list,
                        dir,

                        formatTimestamp,
                        formatBytes,

                        clickRow,
                        dblclickRow,
                        clickSelection,
                        selectedRows,

                        downloadFile,
                        downloadFiles,

                        fileListTableRef,

                        downloadDialogVisible,
                        dlinkList,
                        user_agent,
                        copy,

                        sendDownloadFile,

                        configAria2,
                        configAria2Form,
                        configAria2FormRef,
                        configAria2FormRule,
                        configAria2FormVisible,
                        selectDownloadFiles,
                        selectDownloadFilesChange,
                        sendDownloadFiles,

                        openDownloadListDialog,
                        copyLink
                    }
                }
            })
        ;
        app.use(ElementPlus);
        app.mount("#app");
    </script>
@endsection

@section('style')
    <style>
        .card, .table, .form, .alert {
            margin-top: 15px;
        }
    </style>
@endsection
