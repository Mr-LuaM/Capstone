<template>
  <v-col class="mb-6">
    <v-card max-width="100%" class="bg-background rounded" elevation="2">
      <v-img
        height="200px"
        src=".././assets/img/logo/CHR-PNP-DEPED_banner-1896x800.png"
        cover
      ></v-img>

      <v-row justify="center">
        <v-col
          align-self="start"
          class="d-flex justify-left align-left pa-0 mt-n16 ml-16"
          cols="12"
        >
          <v-avatar
            class="profile avatar-center-height avatar-shadow"
            color="grey"
            size="164"
          >
            <v-img
              src=".././assets/img/examples/avatar_victor_metelskiy_shutterstock_548848999.jpg"
              cover
            >
              <v-btn
                @click="onButtonClick"
                class="upload-btn ml-n6"
                x-large
                icon
              >
                <v-icon>mdi-camera</v-icon>
              </v-btn>
              <input
                ref="uploader"
                class="d-none"
                type="file"
                accept="image/*"
                @change="onFileChanged"
              />
            </v-img>
          </v-avatar>
          <v-icon class="float-left">mdi-camera</v-icon>
        </v-col>
      </v-row>

      <v-list-item color="#0000" class="profile-text-name ma-4">
        <v-list-item-content>
          <v-row class="mt-4">
            <v-col col="12" md="3">
              <LastNameInput divLabel="" customLabel="Last Name" />
            </v-col>
            <v-col col="12" md="3">
              <FirstNameInput divLabel="" customLabel="First Name" />
            </v-col>
            <v-col col="12" md="3">
              <MiddleNameInput divLabel="" customLabel="Middle Name" />
            </v-col>
            <v-col col="12" md="3">
              <NameExtensionInput divLabel="" customLabel="Name Extension" />
            </v-col>
            <v-col col="12" md="1">
              <GenericNumber divLabel="" customLabel="Age" />
            </v-col>

            <v-col cols="6" sm="2">
              <RadioButtonsInput
                v-model="sex"
                labelText=""
                :options="['Male', 'Female']"
                :inline="true"
              />

              <!-- <p>Selected Sex: {{ sex }}</p> -->
            </v-col>
            <v-col col="12" md="3">
              <Address divLabel="" customLabel="Address" />
            </v-col>
            <v-col col="12" md="3">
              <BDateInput divLabel="" labelText="Birthday" />
            </v-col>
            <v-col col="12" md="3">
              <NationalityInput divLabel="" customLabel="Nationality" />
            </v-col>
            <v-col col="12" md="3">
              <ReligionInput divLabel="" customLabel="Religion" />
            </v-col>
            <v-col col="12" md="3">
              <PhoneNumberInput divLabel="" customLabel="Phone Number" />
            </v-col>
          </v-row>
        </v-list-item-content>
      </v-list-item>
    </v-card>
  </v-col>
</template>
<script>
import BDateInput from "@/components/forms/BDateInput.vue";
import GenericNumber from "@/components/forms/Generic/GenericNumber.vue";
import MiddleNameInput from "@/components/forms/MiddleNameInput.vue";
import NameExtensionInput from "@/components/forms/NameExtensionInput.vue";
import NationalityInput from "@/components/forms/NationalityInput.vue";
import PhoneNumberInput from "@/components/forms/PhoneNumberInput.vue";
import ReligionInput from "@/components/forms/ReligionInput.vue";
import Address from "@/components/forms/address/Address.vue";

export default {
  components: {
    MiddleNameInput,
    NameExtensionInput,
    GenericNumber,
    Address,
    BDateInput,
    NationalityInput,
    ReligionInput,
    PhoneNumberInput,
  },
};
</script>
<style>
.gradient-cover {
  position: relative;
}

.gradient-cover::before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: linear-gradient(
    to right,
    hsla(210, 100%, 50%, 1) 0%,
    hsla(0, 100%, 50%, 1) 35%,
    hsla(0, 100%, 50%, 1) 74%
  );
  opacity: 0.7; /* Adjust the opacity as needed */
}

.upload-btn {
  position: absolute !important;
  z-index: 999;
  top: 87%;
  left: 65%;
  transform: translate(-50%, -50%);
  color: #fff; /* White text for better visibility */
  background-color: rgba(0, 0, 0, 0.897); /* Green color for the button */
  padding: 10px 20px;
  border: none;
  border-radius: 55px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Subtle box shadow */
}

.avatar-shadow {
  outline-color: white;
  box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75); /* Adjust the color to PNP blue */
  -webkit-box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75);
  -moz-box-shadow: 0px 0px 10px 0px rgba(0, 82, 165, 0.75);
}
</style>
