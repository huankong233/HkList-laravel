@extends('layouts.main')

@section('title', '后台控制中心')

@section('template')
    <el-dialog
            v-model="addAccountFormVisible"
            title="添加代理账号"
            width="60%"
    >
        <el-form
                ref="addAccountFormRef"
                v-bind:model="addAccountForm"
                v-bind:rules="addAccountFormRule"
                label-width="200px"
        >
            <el-form-item label="账户Cookie" prop="cookie">
                <el-input type="textarea" v-model="addAccountForm.cookie" rows="5"></el-input>
            </el-form-item>
            <el-form-item label="账号名称" prop="username">
                <el-input v-model="addAccountForm.username" disabled></el-input>
            </el-form-item>
            <el-form-item label="账号等级" prop="vipType">
                <el-input v-model="addAccountForm.vipType" disabled></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <span class="dialog-footer">
              <el-button @click="closeAddDialog">取消</el-button>
              <el-button type="primary"
                         @click="getAccountInfo"
                         v-bind:disabled="checkedInfo"
                         v-bind:loading="addAccountForm.pending"
              >
                获取账户信息
              </el-button>
              <el-button type="primary"
                         @click="addAccount(addAccountFormRef)"
                         v-bind:disabled="!checkedInfo"
                         v-bind:loading="addAccountForm.addPending"
              >
                添加
              </el-button>
            </span>
        </template>
    </el-dialog>

    <el-card class="box-card">
        <h2>后台控制中心 | {{ config("app.name") }}</h2>
        <el-tabs v-model="activeName">
            <el-tab-pane label="基础配置" name="changeConfig">
                <el-form
                        ref="changeConfigFormRef"
                        v-bind:model="changeConfigForm"
                        v-bind:rules="changeConfigFormRule"
                        label-width="230px"
                >
                    <el-form-item label="版本号" prop="version">
                        <el-input disabled v-model="changeConfigForm.version"></el-input>
                    </el-form-item>
                    <el-form-item label="下载使用的 User_Agent" prop="user_agent">
                        <el-input v-model="changeConfigForm.user_agent"></el-input>
                    </el-form-item>
                    <el-form-item label="批量解析时休眠时间(秒)" prop="sleep">
                        <el-input v-model="changeConfigForm.sleep"></el-input>
                    </el-form-item>
                    <el-form-item label="批量解析时单次最大解析数量" prop="max_once">
                        <el-input v-model="changeConfigForm.max_once"></el-input>
                    </el-form-item>
                    <el-form-item label="公告开关" prop="announceSwitch">
                        <el-switch
                                v-model="changeConfigForm.announceSwitch"
                                size="large"
                        />
                    </el-form-item>
                    <el-form-item label="公告内容" prop="announce">
                        <el-input type="textarea" v-model="changeConfigForm.announce"></el-input>
                    </el-form-item>
                    <el-form-item label="获取列表时的 Cookie" prop="cookie">
                        <el-input type="textarea" v-model="changeConfigForm.cookie" rows="5"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary"
                                   v-on:click="changeConfig(changeConfigFormRef)"
                                   v-bind:disabled="changeConfigForm.pending"
                                   v-bind:loading="changeConfigForm.pending"
                        >
                            提交
                        </el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
            <el-tab-pane label="修改用户信息" name="changeUserInfo">
                <el-form
                        ref="changeUserInfoFormRef"
                        v-bind:model="changeUserInfoForm"
                        v-bind:rules="changeUserInfoFormRule"
                        label-width="200px"
                >
                    <el-form-item label="新的用户名(为空就是不改)" prop="newUsername">
                        <el-input v-model="changeUserInfoForm.newUsername"></el-input>
                    </el-form-item>
                    <el-form-item label="旧密码" prop="nowPassword">
                        <el-input v-model="changeUserInfoForm.nowPassword"></el-input>
                    </el-form-item>
                    <el-form-item label="新密码" prop="newPassword">
                        <el-input v-model="changeUserInfoForm.newPassword"></el-input>
                    </el-form-item>
                    <el-form-item label="确认新密码" prop="confirmPassword">
                        <el-input v-model="changeUserInfoForm.confirmPassword"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary"
                                   v-on:click="changeUserInfo(changeUserInfoFormRef)"
                                   v-bind:disabled="changeUserInfoForm.pending"
                                   v-bind:loading="changeUserInfoForm.pending"
                        >
                            提交
                        </el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
            <el-tab-pane label="代理账号管理" name="accountManagement">
                <el-button type="primary" @click="openAddDialog">添加代理账号</el-button>
                <el-button type="primary" v-bind:disabled="selectAccounts.length <= 0" @click="enableSelectAccounts">
                    批量启用
                </el-button>
                <el-button type="primary" v-bind:disabled="selectAccounts.length <= 0" @click="blockSelectAccounts">
                    批量禁用
                </el-button>
                <el-table
                        v-loading="accountLoading"
                        v-bind:data="accountList.data"
                        border
                        show-overflow-tooltip
                        class="table"
                        @selection-change="selectAccountsChange"
                >
                    <el-table-column type="selection" width="40"></el-table-column>
                    <el-table-column
                            prop="id"
                            label="编号">
                    </el-table-column>
                    <el-table-column
                            prop="baidu_name"
                            label="百度用户名">
                    </el-table-column>
                    <el-table-column
                            prop="netdisk_name"
                            label="网盘用户名">
                    </el-table-column>
                    <el-table-column
                            prop="state"
                            label="状态">
                    </el-table-column>
                    <el-table-column
                            prop="add_time"
                            label="添加时间">
                    </el-table-column>
                    <el-table-column
                            prop="use"
                            label="最后一次有效时间">
                    </el-table-column>
                    <el-table-column
                            prop="vip_type"
                            label="会员类型">
                    </el-table-column>
                    <el-table-column
                            prop="cookie"
                            label="cookie值">
                    </el-table-column>
                    <el-table-column
                            prop="switch"
                            label="是否启用">
                        <template #default="scope">
                            @{{ scope.row.switch === 0 ? '禁用' : '启用'}}
                        </template>
                    </el-table-column>
                    <el-table-column
                            label="操作"
                            width="160">
                        <template #default="scope">
                            <el-button
                                    size="small"
                                    v-bind:type="scope.row.switch === 0 ? 'success' : 'info'"
                                    @click="switchAccount(scope.row.id,scope.row.switch)"
                            >
                                @{{ scope.row.switch === 0 ? '启用' : '禁用'}}
                            </el-button>
                            <el-button
                                    size="small"
                                    type="danger"
                                    @click="deleteAccount(scope.row.id)"
                            >
                                删除
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <el-pagination
                        v-model:current-page="currentPage"
                        v-model:page-size="pageSize"
                        v-bind:total="accountList?.total"
                        hide-on-single-page
                        @current-change="getAccounts"
                ></el-pagination>
            </el-tab-pane>
            <el-tab-pane label="开源说明" name="openSourceNotice">
                <el-card class="illustrate">
                    <el-text>
                        本程序是免费开源项目，核心代码均未加密，其要旨是为了方便文件分享与下载，重点是GET被没落的PHP语法学习。开源项目所涉及的接口均为官方开放接口，需使用正版SVIP会员账号进行代理提取高速链接，无破坏官方接口行为，本身不存违法。仅供自己参考学习使用。诺违规使用官方会限制或封禁你的账号，包括你的IP，如无官方授权进行商业用途会对你造成更严重后果。源码仅供学习，如无视声明使用产生正负面结果(限速，被封等)与都作者无关。
                    </el-text>
                </el-card>
            </el-tab-pane>
        </el-tabs>
    </el-card>
@endsection

@section('scripts')
    <script>
        const {createApp, ref, onMounted} = Vue
        const {ElMessage} = ElementPlus

        const app = createApp({
            setup() {
                const activeName = ref('changeConfig')

                const changeUserInfoForm = ref({
                    nowPassword: "",
                    newPassword: "",
                    confirmPassword: "",
                    newUsername: "",
                    pending: false
                })

                const changeUserInfoFormRef = ref(null)

                const changeUserInfoFormRule = {
                    nowPassword: [{required: true, message: '请输入旧密码', trigger: 'blur'}],
                    newPassword: [{required: true, message: '请输入新密码', trigger: 'blur'}],
                    confirmPassword: [{required: true, message: '请确认新密码', trigger: 'blur'}]
                }

                const changeUserInfo = async (formEl) => {
                    if (!formEl) return
                    if (await formEl.validate(() => {
                    })) {
                        if (changeUserInfoForm.value.newPassword !== changeUserInfoForm.value.confirmPassword) {
                            ElMessage.error('两次密码不一致')
                            return
                        }

                        changeUserInfoForm.value.pending = true
                        const response = await axios.post("{{route('admin.changeUserInfo')}}", {
                            nowPassword: changeUserInfoForm.value.nowPassword,
                            newPassword: changeUserInfoForm.value.newPassword,
                            confirmPassword: changeUserInfoForm.value.confirmPassword,
                            newUsername: changeUserInfoForm.value.newUsername ?? ""
                        })
                            .catch(error => {
                                const {response: {data: {message}, status}} = error
                                ElMessage.error(status === 400 ? message : '服务器错误')
                            }) ?? 'failed'
                        changeUserInfoForm.value.pending = false

                        if (response !== 'failed') {
                            ElMessage.message("修改成功")
                            setTimeout(() => location.reload(), 1000)
                        }
                    }
                }

                const changeConfigForm = ref({
                    version: "{{config("94list.version")}}",
                    user_agent: "{{config("94list.user_agent")}}",
                    sleep: "{{config("94list.sleep")}}",
                    "max_once": "{{config("94list.max_once")}}",
                    announceSwitch: {{config("94list.announceSwitch")}} === 1,
                    announce: "{{config("94list.announce")}}",
                    cookie: "{{config("94list.cookie")}}",
                    pending: false
                })

                const changeConfigFormRef = ref(null)

                const changeConfigFormRule = {
                    user_agent: [{required: true, message: '请输入User_Agent', trigger: 'blur'}],
                    announce: [{required: true, message: '请公告内容', trigger: 'blur'}],
                    announceSwitch: [{required: true, message: '请确认开关状态', trigger: 'blur'}],
                    cookie: [{required: true, message: '请输入获取列表时的 Cookie', trigger: 'blur'}],
                    "max_once": [{required: true, message: '请输入批量解析时单次最大解析数量', trigger: 'blur'}],
                    sleep: [{required: true, message: '请输入批量解析时休眠时间(秒)', trigger: 'blur'}],
                }

                const changeConfig = async (formEl) => {
                    if (!formEl) return
                    if (await formEl.validate(() => {
                    })) {
                        changeConfigForm.value.pending = true

                        const response = await axios.post("{{route('admin.changeConfig')}}", {
                            user_agent: changeConfigForm.value.user_agent,
                            announceSwitch: changeConfigForm.value.announceSwitch,
                            announce: changeConfigForm.value.announce,
                            cookie: changeConfigForm.value.cookie,
                            sleep: changeConfigForm.value.sleep,
                            "max_once": changeConfigForm.value["max_once"]
                        }).catch(error => {
                            const {response: {data: {message}, status}} = error
                            ElMessage.error(status === 400 ? message : '服务器错误')
                        }) ?? "failed"

                        changeConfigForm.value.pending = false

                        if (response !== 'failed') {
                            ElMessage.success('修改成功')
                        }
                    }
                }

                const vipTypeMap = new Map([
                    [0, '普通用户'],
                    [1, '普通会员'],
                    [2, '超级会员']
                ])

                const addAccountFormVisible = ref(false)

                const checkedInfo = ref(false)

                const openAddDialog = () => {
                    closeAddDialog()
                    addAccountFormVisible.value = true
                }
                const closeAddDialog = () => {
                    addAccountForm.value.cookie = ''
                    addAccountForm.value.username = ''
                    addAccountForm.value.vipType = ''
                    addAccountFormVisible.value = false
                    checkedInfo.value = false
                }

                const addAccountForm = ref({
                    cookie: "",
                    username: "",
                    vipType: "",
                    pending: false,
                    addPending: false
                })

                const addAccountFormRef = ref(null)

                const addAccountFormRule = {
                    cookie: [{required: true, message: '请输入账户Cookie', trigger: 'blur'}]
                }

                const getAccountInfo = async () => {
                    if (!addAccountFormRef.value) return
                    if (await addAccountFormRef.value.validate(() => {
                    })) {
                        addAccountForm.value.pending = true
                        const response = await axios.post("{{route("admin.getAccountInfo")}}", {
                            cookie: addAccountForm.value.cookie.trim()
                        }).catch(error => {
                            const {response: {data: {message}, status}} = error
                            ElMessage.error(status === 500 ? message : '服务器错误')
                        }) ?? 'failed'

                        addAccountForm.value.pending = false

                        if (response !== 'failed') {
                            const {data: {data}} = response
                            checkedInfo.value = true
                            addAccountForm.value.username = data.netdisk_name
                            addAccountForm.value.vipType = vipTypeMap.get(data.vip_type)
                        }
                    }
                }

                const addAccount = async (formEl) => {
                    if (!formEl) return
                    if (await formEl.validate(() => {
                    })) {
                        addAccountForm.value.addPending = true

                        const response = await axios.post("{{route('admin.addAccount')}}", {
                            cookie: addAccountForm.value.cookie.trim()
                        }).catch(error => {
                            const {response: {data: {message}, status}} = error
                            ElMessage.error(status === 400 ? message : '服务器错误')
                            checkedInfo.value = false
                            addAccountForm.value.username = ""
                            addAccountForm.value.vipType = ""
                        }) ?? 'failed'

                        addAccountForm.value.addPending = false

                        if (response !== 'failed') {
                            closeAddDialog()
                            ElMessage.success('添加成功')
                            await getAccounts()
                        }
                    }
                }

                const currentPage = ref(1)
                const pageSize = ref(10)
                const accountList = ref([])
                const accountLoading = ref(false)

                const getAccounts = async () => {
                    accountLoading.value = true
                    const response = await axios.post(`{{route('admin.getAccounts')}}?page=${currentPage.value}`, {
                        size: pageSize.value
                    }).catch(error => {
                        const {response: {data: {message}, status}} = error
                        ElMessage.error(status === 400 ? message : '服务器错误')
                    }) ?? 'failed'

                    accountLoading.value = false

                    if (response !== 'failed') {
                        const {data: {data}} = response
                        accountList.value = data
                    }
                }

                onMounted(getAccounts)

                const switchAccount = async (userId, state) => {
                    const response = await axios.post("{{route('admin.switchAccount')}}", {
                        account_id: userId
                    }).catch(error => {
                        const {response: {data: {message}, status}} = error
                        ElMessage.error(status === 400 ? message : '服务器错误')
                    }) ?? 'failed'

                    if (response !== 'failed') {
                        ElMessage.success(`成功${state === 0 ? '启用' : '禁用'}`)
                        await getAccounts();
                    }
                }

                const deleteAccount = async (userId) => {
                    const response = await axios.post("{{route('admin.deleteAccount')}}", {
                        account_id: userId
                    }).catch(error => {
                        const {response: {data: {message}, status}} = error
                        ElMessage.error(status === 400 ? message : '服务器错误')
                    }) ?? 'failed'

                    if (response !== 'failed') {
                        ElMessage.success(`删除账户成功`)
                        await getAccounts();
                    }
                }

                const selectAccounts = ref([])
                const selectAccountsChange = (row) => selectAccounts.value = row

                const enableSelectAccounts = () => {
                    selectAccounts.value.forEach(item => {
                        if (item.switch === 1) return ElMessage.info(`编号:${item.id},已经启用了`)
                        switchAccount(item.id, item.switch)
                    })
                }

                const blockSelectAccounts = () => {
                    selectAccounts.value.forEach(item => {
                        if (item.switch === 0) return ElMessage.info(`编号:${item.id},已经禁用了`)
                        switchAccount(item.id, item.switch)
                    })
                }

                return {
                    activeName,

                    changeConfig,
                    changeConfigForm,
                    changeConfigFormRef,
                    changeConfigFormRule,

                    changeUserInfo,
                    changeUserInfoForm,
                    changeUserInfoFormRef,
                    changeUserInfoFormRule,

                    vipTypeMap,
                    openAddDialog,
                    closeAddDialog,
                    addAccount,
                    getAccountInfo,
                    checkedInfo,
                    addAccountForm,
                    addAccountFormRef,
                    addAccountFormRule,
                    addAccountFormVisible,

                    accountList,
                    accountLoading,
                    pageSize,
                    currentPage,
                    getAccounts,
                    switchAccount,
                    deleteAccount,

                    selectAccounts,
                    selectAccountsChange,
                    enableSelectAccounts,
                    blockSelectAccounts
                }
            }
        })

        app.use(ElementPlus)
        app.mount('#app')
    </script>
@endsection

@section('style')
    <style>
        .table {
            margin-top: 20px;
        }
    </style>
@endsection
