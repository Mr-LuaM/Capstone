import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import vuetify from "./plugins/vuetify";
import { loadFonts } from "./plugins/webfontloader";
import "../src/assets/css/main.css";
import axios from "axios";
axios.defaults.baseURL = "http://backend.test/";

// Global components
import infoSnack from "./components/snackbars/infoSnack.vue";
import ThemeSwitcher from "./components/buttons/ThemeSwitcher.vue";
import FileInput from "./components/forms/FileInput.vue";
import LastNameInput from "./components/forms/LastNameInput.vue";
import FirstNameInput from "./components/forms/FirstNameInput.vue";
import MiddleNameInput from "./components/forms/MiddleNameInput.vue";
import NameExtensionInput from "./components/forms/NameExtensionInput.vue";
import RadioButtonsInput from "./components/forms/RadioButtonsInput.vue";
import BDateInput from "./components/forms/BDateInput.vue";
import NationalityInput from "./components/forms/NationalityInput.vue";
import ReligionInput from "./components/forms/ReligionInput.vue";
import HeightInput from "./components/forms/HeightInput.vue";
import WeightInput from "./components/forms/WeightInput.vue";
import PasswordInput from "./components/forms/PasswordInput.vue";
import ConfirmPasswordInput from "./components/forms/ConfirmPasswordInput.vue";
import CourseSelection from "./components/forms/CourseSelection.vue";
import GenericAutocomplete from "./components/forms/Generic/GenericAutocomplete.vue";
import GenericTextField from "./components/forms/Generic/GenericTextField.vue";
//address
import Address from "./components/forms/address/Address.vue";

import EmailInput from "./components/forms/EmailInput.vue";
import PhoneNumberInput from "./components/forms/PhoneNumberInput.vue";

//courses selection
import ProgramAutocomplete from "./components/forms/CourseSelection/ProgramAutocomplete.vue";
import StationAutocomplete from "./components/forms/CourseSelection/StationAutocomplete.vue";

import warning from "./components/dialogs/warning.vue";
import success from "./components/dialogs/success.vue";
import confirmationModal from "./components/dialogs/confirmationModal.vue";
loadFonts();

const app = createApp(App);

// Register the ThemeSwitcher component
app.component("infoSnack", infoSnack);
app.component("ThemeSwitcher", ThemeSwitcher);
app.component("FileInput", FileInput);
app.component("LastNameInput", LastNameInput);
app.component("FirstNameInput", FirstNameInput);
app.component("MiddleNameInput", MiddleNameInput);
app.component("NameExtensionInput", NameExtensionInput);
app.component("RadioButtonsInput", RadioButtonsInput);
app.component("BDateInput", BDateInput);
app.component("NationalityInput", NationalityInput);
app.component("ReligionInput", ReligionInput);
app.component("HeightInput", HeightInput);
app.component("WeightInput", WeightInput);
app.component("PasswordInput", PasswordInput);
app.component("ConfirmPasswordInput", ConfirmPasswordInput);
app.component("CourseSelection", CourseSelection);
//address
app.component("Address", Address);

app.component("EmailInput", EmailInput);
app.component("PhoneNumberInput", PhoneNumberInput);

app.component("ProgramAutocomplete", ProgramAutocomplete);
app.component("StationAutocomplete", StationAutocomplete);

app.component("warning", warning);
app.component("success", success);
app.component("confirmationModal", confirmationModal);
app.component("GenericAutocomplete", GenericAutocomplete);
app.component("GenericTextField", GenericTextField);
app.use(router).use(vuetify).mount("#app");
