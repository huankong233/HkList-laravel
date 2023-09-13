@extends('layouts.main')

@section('title', '前台解析中心')

@section('template')
    <el-dialog
            v-model="Announce.switch"
            title="公告"
            width="90%"
    >
        <span>@{{Announce.message}}</span>
    </el-dialog>

    <el-dialog v-model="downloadDialogVisible" title="解析任务列表" width="80%">
        <el-space wrap> 当前的UA :
            <el-link type="danger" @click="copy(user_agent,'已复制UA')">@{{ user_agent }}</el-link>
        </el-space>
        <el-space wrap>
            <el-button type="primary" @click="sendDownloadFiles">批量下载</el-button>
            <el-button type="primary" @click="openDownloadListDialog">下载配置</el-button>
        </el-space>
        <el-table
                border
                show-overflow-tooltip
                class="table"
                :data="dlinkList"
                @selection-change="sendDownloadFiles">
            <el-table-column type="selection" width="40"></el-table-column>
            <el-table-column prop="filename" label="文件名"></el-table-column>
            <el-table-column prop="dlink" label="下载链接"></el-table-column>
            <el-table-column label="操作" width="280">
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
                            @click="sendDown(scope.row.dlink,scope.row.filename)"
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
        <el-form
                ref="getFileListFormRef"
                v-bind:model="getFileListForm"
                v-bind:rules="getFileListFormRule"
                label-width="100"
        >
            <el-form-item label="链接" prop="url">
                <el-input v-model="getFileListForm.url" @blur="checkLink"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password">
                <el-input v-model="getFileListForm.password"></el-input>
            </el-form-item>
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
                           v-on:click="freshFileList"
                >
                    重新获取当前路径文件
                </el-button>
                <el-button type="primary"
                           v-bind:disabled="selectedRows.length <= 0"
                           v-on:click="downloadFiles"
                >
                    批量解析
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
                  @row-dblclick="clickRow"
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
            <el-table-column label="修改时间" width="180">
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
        const {createApp, ref} = Vue
        const {ElMessage} = ElementPlus

        const app = createApp({
                setup() {
                    const Announce = ref({
                        switch: false,
                        message: "{{config("94list.announce")}}"
                    })

                    // 淡入效果
                    setTimeout(() => Announce.value.switch = {{config("94list.announceSwitch")}} === 1, 300)

                    const getFileListForm = ref({
                        url: "https://pan.baidu.com/s/1AHSE9K1EpL2ga1ldU88C5A",
                        password: "j94h",
                        pending: false
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
                    const dir = ref("/")

                    const getFileListClickEvent = async (formEl) => {
                        if (!formEl) return
                        if (!await formEl.validate(() => {
                        })) return

                        getFileListForm.value.pending = true

                        dir.value = "/"
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
                        const fileList = await axios.post("{{route('user.getFileList')}}", {
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

                    const freshFileList = async () => {
                        getFileListForm.value.pending = true
                        await getFileList(0, true)
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
                        const response = await axios.post("{{route('user.getSign')}}", {
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

                    const downloadFile = async (fs_id, server_filename) => {
                        const response = await axios.post("{{route('user.downloadFile')}}", {
                            fs_id,
                            server_filename,
                            timestamp: timestamp.value,
                            uk: uk.value,
                            sign: sign.value,
                            randsk: randsk.value,
                            shareid: shareid.value
                        }).catch(async error => {
                            const {response: {data: {message}, status}} = error
                            if (message.includes("当前签名已过期")) {
                                ElMessage.error(message)
                                ElMessage.success("自动重新获取中...")
                                await getFileSign()
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

                    const downloadFiles = () => {
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

                        if (selectedRows.value.length > 0) {
                            console.log(selectedRows.value)
                        }
                    }

                    const getDir = async (path, server_mtime) => {
                        dir.value = path
                        await getFileList(server_mtime)
                    }

                    const clickRow = async (scope) => {
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
                        const response = await axios.post(`http://${configDownload.value.host}:${configDownload.value.port}/jsonrpc`, {
                            jsonrpc: '2.0',
                            id: "{{config("app.name")}}",
                            method: 'aria2.addUri',
                            params: [
                                configDownload.value.secret,
                                [dlink],
                                {
                                    'out': filename,
                                    'header': ['User-Agent:' + user_agent.value]
                                }
                            ]
                        }).catch(error => {
                            ElMessage.error('发送失败，可能相对应的下载器没有启动')
                        }) ?? 'failed'

                        if (response !== 'failed') {
                            ElMessage.success('已把 ' + filename + ' 任务发送给下载器')
                        }
                    }

                    const selectDownloadFiles = ref([])
                    const downloadListClick = () => {
                        console.log(selectDownloadFiles.value)
                    }

                    const sendDownloadFiles = (row) => selectDownloadFiles.value = row

                    const configDownload = ref({
                        visible: false,
                        host: "localhost",
                        port: "6800",
                        secret: ""
                    })

                    const openDownloadListDialog = () => configDownload.value.visible = true

                    const closeDownloadListDialog = () => {
                        configDownload.value.visible = false
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

                        configDownload,
                        sendDownloadFiles,
                        downloadListClick,

                        openDownloadListDialog,
                        closeDownloadListDialog
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
        .card {
            margin-top: 15px;
        }

        .table {
            margin-top: 15px;
        }
    </style>
@endsection
