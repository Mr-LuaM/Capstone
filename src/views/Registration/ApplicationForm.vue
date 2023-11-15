<template>
  <v-app>
    <v-fade-transition mode="out-in">
      <v-row>
        <v-col
          cols="12"
          sm="10"
          class="d-flex justify-center align-center mx-auto"
        >
          <!-- Updated cols attribute -->

          <v-card variant="flat">
            <v-row>
              <!-- Adjust the column size as needed -->
              <v-img
                src="@/assets/img/logo/CHR-PNP-DEPED_banner-1896x800.png"
                height="125"
                cover
                class="bg-grey-lighten-2"
              >
                <v-col class="d-flex justify-end align-center">
                  <ThemeSwitcher color="secondary" />
                </v-col>
              </v-img>
            </v-row>
            <v-card-title class="text-center font-weight-bold text-lg-h4 pt-5">
              PNPeduLink
            </v-card-title>
            <v-card-title class="text-center font-weight-bold text-l-h5 pb-5">
              Online Application
            </v-card-title>
            <v-card-text>
              <v-alert
                type="info"
                variant="tonal"
                border="start"
                class="text-justify"
              >
                Welcome to PNPeduLink's Online Application Portal. This platform
                is your gateway to a seamless application journey with us. Here,
                you can stay updated on the progress of your application,
                receive important updates, and access additional information
                related to your application. If you're new to PNPeduLink or have
                forgotten your Application Number or PIN, we've got you covered.
                Simply <a href="#">click here</a> to verify your application or
                recover your Application Number / PIN.
              </v-alert>
              <v-alert
                class="mt-3 text-justify"
                type="warning"
                variant="tonal"
                close-label="close"
              >
                Gmail users may be experiencing issues receiving emails from our
                domain. We understand the significance of smooth communication,
                and we are actively working to address this matter. If you do
                not receive the email shortly after submitting your application,
                please <a href="#">click here.</a></v-alert
              >
            </v-card-text>
            <v-card-title class="text-center font-weight-bold text-lg-h5 pt-3">
              AY
              {{ new Date().getFullYear() }} -
              {{ new Date().getFullYear() + 1 }}
            </v-card-title>

            <v-card-text> </v-card-text>
            <v-container fluid variant="flat">
              <Stepper />
            </v-container>
          </v-card>
        </v-col>
      </v-row>
    </v-fade-transition>
  </v-app>
</template>

<script>
import ThemeSwitcher from "@/components/buttons/ThemeSwitcher.vue";
import Stepper from "../../components/Application/Stepper";

export default {
  name: "ApplicationForm",
  components: {
    Stepper,
    ThemeSwitcher,
  },
};
</script>
