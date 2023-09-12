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

    {{--    <el-dialog v-model="DownDialog" title="解析任务列表" width="80%">--}}
    {{--        <el-space wrap> 当前的UA :--}}
    {{--            <el-link type="danger" @click="copy(user_agent,'已复制UA')">{{ user_agent }}</el-link>--}}
    {{--        </el-space>--}}
    {{--        <br><br>--}}
    {{--        <el-table :data="rw_list" show-overflow-tooltip>--}}
    {{--            <el-table-column property="name" label="文件名" width="180" fixed></el-table-column>--}}
    {{--            <el-table-column label="下载链接" width="480">--}}
    {{--                <template #default="scope">--}}
    {{--                    {{ scope.row.dlink }}--}}
    {{--                </template>--}}
    {{--            </el-table-column>--}}
    {{--            <el-table-column label="操作" width="280">--}}
    {{--                <template #default="scope">--}}
    {{--                    <template v-if="scope.row.DownState=='0'">--}}
    {{--                        <el-button @click="copy(scope.row.dlink,'已将链接复制到粘贴板内')" type="text" size="small">--}}
    {{--                            复制链接--}}
    {{--                        </el-button>--}}
    {{--                        <el-button type="text" size="small" @click="senddown(scope.row.dlink,scope.row.name,'6800')">--}}
    {{--                            发送Aria2--}}
    {{--                        </el-button>--}}
    {{--                        <el-button type="text" size="small" @click="senddown(scope.row.dlink,scope.row.name,'16800')">--}}
    {{--                            发送Motrix--}}
    {{--                        </el-button>--}}
    {{--                    </template>--}}
    {{--                    <template v-if="scope.row.DownState=='1'">--}}
    {{--                    </template>--}}
    {{--                </template>--}}
    {{--            </el-table-column>--}}
    {{--        </el-table>--}}
    {{--    </el-dialog>--}}

    <el-card>
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
                           v-bind:disabled="getFileListForm.pending"
                           v-bind:loading="getFileListForm.pending"
                >
                    解析链接
                </el-button>
                <el-button type="primary"
                           v-on:click="freshFileList"
                           v-bind:disabled="getFileListForm.pending"
                           v-bind:loading="getFileListForm.pending"
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

    <el-card class="card">
        <el-table stripe
                  ref="fileListTableRef"
                  v-bind:data="list"
                  v-loading="getFileListForm.pending"
                  @row-dblclick="clickRow"
                  @selection-change="clickSelection">
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column label="文件名" width="280">
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
                        url: "https://pan.baidu.com/s/1mvGM7nXznzMiNbAMKpHE4w",
                        password: "kwn8",
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

                    const getFileList = async (server_mtime, refresh = false) => {
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

                        console.log(data)

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
                        const getSign = await axios.post("{{route('user.getSign')}}", {
                            uk: uk.value,
                            shareid: shareid.value
                        }).catch(error => {
                            const {response: {data: {message}, status}} = error
                            ElMessage.error(status === 400 ? message : '服务器错误')
                        }) ?? "failed"

                        if (getSign === 'failed') return

                        const {data: {message, data}} = getSign
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
                        console.log(fs_id, server_filename)
                    }

                    const downloadFiles = () => {
                        console.log(selectedRows.value.filter(item => item.is_dir === '1' || item.is_dir === 1))
                        selectedRows.value = selectedRows.value.filter(item => {
                            const bool = item.is_dir === '1' || item.is_dir === 1
                            if (!bool) {
                                ElMessage.error("请勿勾选文件夹")
                                fileListTableRef.value?.clearSelection()
                            }
                            return bool
                        })
                        console.log(selectedRows.value)
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

                    const clickSelection = (row) => {
                        selectedRows.value = row
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

                        fileListTableRef
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
    </style>
@endsection
