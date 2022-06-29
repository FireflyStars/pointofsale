import MainHeader from '../components/layout/MainHeader';
import SideBar from '../components/layout/SideBar';
import PageTitle from '../components/layout/PageTitle';
import Icon from '../components/miscellaneous/Icon'
import CheckBox from '../components/miscellaneous/CheckBox'
import BaseButton from '../components/miscellaneous/BaseButton'
import SelectBox from '../components/miscellaneous/SelectBox'
import Dropdown from '../components/miscellaneous/Dropdown'
import Modal from '../components/miscellaneous/Modal'
import MultiSelect from "../components/miscellaneous/MultiSelect.vue"
import TabPane from "../components/miscellaneous/TabPane.vue"
import loader from '../components/miscellaneous/Loader'
import DatePicker from '../components/miscellaneous/DatePicker.vue'
import SimpleModalPopup from '../components/miscellaneous/simplemodal/SimpleModalPopup.vue';

export default function globalComponents(app) {
    app.component('MultiSelect', MultiSelect)
    app.component('MainHeader', MainHeader)
    app.component('SideBar', SideBar)
    app.component('PageTitle', PageTitle)
    app.component('TabPane', TabPane)
    app.component('Icon', Icon)
    app.component('CheckBox', CheckBox)
    app.component('BaseButton', BaseButton)
    app.component('SelectBox', SelectBox)
    app.component('Dropdown', Dropdown)
    app.component('Modal', Modal)
    app.component('loader', loader)
    app.component('DatePicker', DatePicker)
    app.component('SimpleModalPopup', SimpleModalPopup)
}