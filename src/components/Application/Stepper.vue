<template>
  <div>
    <v-stepper v-model="currentStep" variant="flat">
      <template v-slot:default="{ prev, next }">
        <v-stepper-header>
          <template v-for="(step, index) in steps" :key="step.value">
            <v-divider v-if="index !== 0"></v-divider>

            <v-stepper-item
              :title="step.title"
              :value="step.value"
              :complete="step.complete"
              :editable="step.editable"
              :rules="step.rules"
              :color="step.color"
              :subtitle="step.subtitle"
            ></v-stepper-item>
          </template>

          <!-- Define stepper items in a loop -->
          <div></div>
        </v-stepper-header>

        <v-stepper-window>
          <v-stepper-window-item :key="1" :value="1">
            <v-card>
              <v-row>
                <!-- First Column -->
                <v-col cols="12" md="6">
                  <v-card>
                    <v-img
                      src="@/assets/img/logo/CHR-PNP-DEPED_banner-1896x800.png"
                      height="125"
                      cover
                      class="bg-grey-lighten-2"
                    ></v-img>
                    <v-carousel>
                      <!-- Add your carousel items here -->
                    </v-carousel>
                  </v-card>
                </v-col>

                <!-- Second Column -->
                <v-col cols="12" md="6">
                  <v-card>
                    <v-card-title>Second Column</v-card-title>
                    <v-card-text>
                      Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                      Quisque vel libero ac tellus lacinia vestibulum. Cras in
                      arcu ut metus euismod suscipit.
                    </v-card-text>
                    <v-card-actions>
                      <v-btn color="primary" @click="next">Start</v-btn>
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>
            </v-card>
          </v-stepper-window-item>
          <v-form ref="form" @submit.prevent="submitApplication">
            <v-stepper-window-item :key="2" :value="2">
              <v-card>
                <v-card-title>Second Column</v-card-title>
                <v-card-text>
                  <!-- Data Privacy Alert -->
                  <v-alert type="warning" variant="text">
                    <v-alert-text class="mt-3 text-justify">
                      Pursuant to Republic Act No. 10173, also known as the Data
                      Privacy Act of 2012, the Batangas State University, the
                      National Engineering University recognizes its commitment
                      to protect and respect the privacy of its customers and/or
                      stakeholders and ensure that all information collected
                      from them are all processed in accordance with the
                      principles of transparency, legitimate purpose, and
                      proportionality mandated under the Data Privacy Act of
                      2012.
                    </v-alert-text>
                  </v-alert>
                  <v-checkbox
                    v-model="agreed"
                    :rules="[(v) => !!v || 'This field is required']"
                  >
                    <template v-slot:label>
                      <div>
                        I agree to PNP
                        <a target="_blank" href="#" v-bind="props" @click.stop>
                          terms and condition
                        </a>
                      </div>
                    </template>
                  </v-checkbox>
                </v-card-text>
              </v-card>
            </v-stepper-window-item>

            <v-stepper-window-item :key="3" :value="3">
              <v-container fluid>
                <v-radio-group
                  color="primary"
                  v-model="academicStatus"
                  :rules="[(v) => !!v || 'This field is required']"
                >
                  <label
                    class="font-weight-bold"
                    style="text-decoration: underline"
                  >
                    1. Academic Status
                  </label>
                  <v-radio
                    label="Graduated Senior High School student"
                    value="Graduated Senior High School student"
                  ></v-radio>
                  <v-radio
                    label="Graduated College Student"
                    value="Graduated College Student"
                  ></v-radio>
                </v-radio-group>

                <v-radio-group
                  color="primary"
                  v-model="enrolledInPNP"
                  :rules="[(v) => !!v || 'This field is required']"
                >
                  <label
                    class="font-weight-bold"
                    style="text-decoration: underline"
                  >
                    2. Is the applicant already enrolled (or was enrolled) in a
                    program in PNP?
                  </label>
                  <v-radio label="Yes" value="Yes"></v-radio>
                  <v-radio label="No" value="No"></v-radio>
                </v-radio-group>

                <v-radio-group
                  color="primary"
                  v-model="firstTimeApplyPNP"
                  :rules="[(v) => !!v || 'This field is required']"
                >
                  <label
                    class="font-weight-bold"
                    style="text-decoration: underline"
                  >
                    3. Is this the applicant’s first time to apply for PNP
                    education program?
                  </label>
                  <v-radio label="Yes" value="Yes"></v-radio>
                  <v-radio label="No" value="No"></v-radio>
                </v-radio-group>
              </v-container>
            </v-stepper-window-item>
            <!-- Personal Information & Photo Requirements Step -->
            <v-stepper-window-item :key="4" :value="4" color="primary" dark>
              <v-card>
                <v-card
                  ><v-card
                    class="mx-auto"
                    color="primary"
                    variant="flat"
                    title="Personal Information"
                  ></v-card>
                </v-card>

                <v-card>
                  <div>
                    <v-row>
                      <!-- First Column - For Adding and Displaying Picture -->
                      <v-col cols="12" sm="4">
                        <v-container>
                          <v-card variant="outlined" class="pa-5">
                            <v-img
                              class="bg-white"
                              :aspect-ratio="1"
                              :src="getFileImageUrl()"
                            ></v-img>
                            <FileInput
                              :FileConfig="fileConfig1"
                              v-model="selectedFile1"
                              @update:value="selectedFile1 = $event"
                              ref="FileInput"
                            />
                            <p>
                              {{
                                selectedFile1[0] ? selectedFile1[0].name : ""
                              }}
                            </p>
                          </v-card>
                        </v-container>
                      </v-col>

                      <!-- Second Column - For Picture Requirements and Example -->
                      <v-col cols="12" sm="8" class="pb-10">
                        <div class="d-flex justify-center mb-3 mr-8">
                          <v-alert
                            class="text-center d-flex align-center mt-8"
                            type="info"
                            variant="elevated"
                            text="Picture Requirements"
                          ></v-alert>
                        </div>

                        <v-card
                          variant="outlined"
                          class="pa-10 mr-5"
                          style="max-height: 400px; overflow-y: auto"
                        >
                          <!-- Right Column - Picture Example -->

                          <v-row>
                            <v-col cols="6">
                              <v-container>
                                <!-- List the picture requirements using ul and li elements -->
                                <ul>
                                  <li>Recent 2x2 picture</li>
                                  <li>Plain white background</li>
                                  <li>Without any worn electronic devices</li>
                                  <li>You must not wear eyeglasses</li>
                                  <li>Taken within the last 6 months</li>
                                  <li>
                                    Show you facing forwards with your head and
                                    shoulders in the photo (80% of the photo
                                    should be your head and shoulders)
                                  </li>
                                  <li>Show you in the center of the photo</li>
                                  <li>No name tag at the bottom part</li>
                                  <li>Not showing extreme hair color</li>
                                  <li>Wearing appropriate and decent attire</li>
                                  <li>Less than 2mb in file size</li>
                                </ul>
                              </v-container>
                            </v-col>
                            <!-- Right Column - Picture Example -->
                            <v-col cols="6">
                              <v-container>
                                <v-img
                                  src="@/assets/img/examples/avatar_victor_metelskiy_shutterstock_548848999.jpg"
                                  max-height="200"
                                  contain
                                ></v-img>
                              </v-container>
                            </v-col>
                          </v-row>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>

                  <v-container>
                    <v-divider color="primary" thickness="3"> </v-divider>
                  </v-container>

                  <div>
                    <v-container fluid>
                      <v-card variant="outlined">
                        <v-card-title> Personal Information </v-card-title>

                        <v-card-text>
                          <v-row>
                            <v-col cols="12" sm="4">
                              <LastNameInput v-model="lastName" />
                            </v-col>

                            <v-col cols="12" sm="4">
                              <FirstNameInput
                                v-model="firstName"
                              ></FirstNameInput>
                            </v-col>
                            <v-col cols="12" sm="2">
                              <MiddleNameInput v-model="middleName" />
                            </v-col>
                            <v-col cols="12" sm="2">
                              <NameExtensionInput v-model="nameExtension" />
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col cols="6" sm="2">
                              <RadioButtonsInput
                                v-model="sex"
                                labelText="Sex"
                                :options="['Male', 'Female']"
                                :inline="true"
                              />

                              <!-- <p>Selected Sex: {{ sex }}</p> -->
                            </v-col>

                            <v-col cols="6" sm="2">
                              <BDateInput
                                v-model="Bdate"
                                divLabel="Birthdate"
                                :min-date="minDate"
                                :max-date="
                                  new Date().toISOString().substr(0, 10)
                                "
                              />
                            </v-col>
                            <v-col cols="12" sm="4">
                              <NationalityInput
                                v-model="nationality"
                                divLabel="Nationality"
                                :cache-items="true"
                              />
                            </v-col>
                            <v-col cols="12" sm="4"
                              ><ReligionInput
                                v-model="religion"
                                divLabel="Religion"
                            /></v-col>
                          </v-row>
                          <v-row>
                            <v-col cols="6" sm="2"
                              ><HeightInput
                                v-model="height"
                                divLabel="Height (in cm)"
                                type="number"
                            /></v-col>
                            <v-col cols="6" sm="2"
                              ><WeightInput
                                v-model="weight"
                                divLabel="Weight (in kg)"
                                customPlaceholder="Enter your weight"
                            /></v-col>
                            <v-col cols="12" sm="4">
                              <Address v-model="address" divLabel="Address" />
                            </v-col>
                            <v-col cols="12" sm="4">
                              <EmailInput v-model="email" />
                            </v-col>
                            <v-col cols="12" sm="4">
                              <PhoneNumberInput v-model="phoneNumber" />
                            </v-col>
                          </v-row>

                          <!-- other personal fields here -->
                        </v-card-text>
                      </v-card>
                    </v-container>
                  </div>
                </v-card>
              </v-card>
            </v-stepper-window-item>
            <v-stepper-window-item :key="5" :value="5" color="primary" dark>
              <v-card>
                <v-card-title>Program to Enroll</v-card-title>
                <v-card-text>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-table fixed-header height="400px">
                        <thead>
                          <tr>
                            <th class="text-left">Course Name</th>
                            <th class="text-left">Stations Offering</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="course in courses" :key="course.Course_ID">
                            <td>{{ course.Course_Name }}</td>
                            <td>
                              <span
                                v-for="(
                                  station, index
                                ) in course.Stations_Offering"
                                :key="index"
                              >
                                {{ station.Station_Name }}
                                <span
                                  v-if="
                                    index < course.Stations_Offering.length - 1
                                  "
                                >
                                  ||
                                </span>
                              </span>
                            </td>
                          </tr>
                        </tbody>
                      </v-table>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-alert
                        class="mt-3"
                        type="info"
                        variant="tonal"
                        border="start"
                        border-color="primary"
                        text="Choose and rank three (3) programs based on your interest."
                      ></v-alert>
                      <v-container>
                        <div><h5>First Choice</h5></div>
                        <v-row>
                          <v-col cols="12" sm="6" md="6">
                            <div class="text-subtitle-1 text-medium-emphasis">
                              Course
                            </div>
                            <Program-Autocomplete
                              v-model="course1"
                              :choices="
                                courses
                                  ? courses.map((course) => course.Course_Name)
                                  : []
                              "
                              divLabel="Courses"
                              @item-selected="handleCourseSelected1"
                            ></Program-Autocomplete>
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <div class="text-subtitle-1 text-medium-emphasis">
                              Station
                            </div>
                            <Program-Autocomplete
                              v-model="station1"
                              :choices="filteredStations1"
                              divLabel="Station"
                              @item-selected="handleStationSelected1"
                            ></Program-Autocomplete>
                          </v-col>
                        </v-row>

                        <div><h5>Second Choice</h5></div>
                        <v-row>
                          <v-col cols="12" sm="6" md="6">
                            <div class="text-subtitle-1 text-medium-emphasis">
                              Course
                            </div>
                            <Program-Autocomplete
                              v-model="course2"
                              :choices="
                                courses
                                  ? courses.map((course) => course.Course_Name)
                                  : []
                              "
                              divLabel="Courses"
                              @item-selected="handleCourseSelected2"
                            ></Program-Autocomplete>
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <div class="text-subtitle-1 text-medium-emphasis">
                              Station
                            </div>
                            <Program-Autocomplete
                              v-model="station2"
                              :choices="filteredStations2"
                              divLabel="Station"
                              @item-selected="handleStationSelected2"
                            ></Program-Autocomplete>
                          </v-col>
                        </v-row>

                        <div><h5>Third Choice</h5></div>
                        <v-row>
                          <v-col cols="12" sm="6" md="6">
                            <div class="text-subtitle-1 text-medium-emphasis">
                              Course
                            </div>
                            <Program-Autocomplete
                              v-model="course3"
                              :choices="
                                courses
                                  ? courses.map((course) => course.Course_Name)
                                  : []
                              "
                              divLabel="Courses"
                              @item-selected="handleCourseSelected3"
                            ></Program-Autocomplete>
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <div class="text-subtitle-1 text-medium-emphasis">
                              Station
                            </div>
                            <Program-Autocomplete
                              v-model="station3"
                              :choices="filteredStations3"
                              divLabel="Station"
                              @item-selected="handleStationSelected3"
                            ></Program-Autocomplete>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-stepper-window-item>
            <v-stepper-window-item :key="6" :value="6">
              <v-card title="Form Preview" flat color="primary"> </v-card>
              <v-alert
                type="warning"
                variant="tonal"
                border="start"
                border-color="primary"
                text="Make sure to write here your correct email and remember your password"
                class="my-3"
              ></v-alert>
              <v-row>
                <v-col cols="12" sm="4">
                  <EmailInput v-model="email" />
                </v-col>

                <v-col cols="12" sm="4">
                  <PasswordInput v-model="password" />
                </v-col>
                <v-col cols="12" sm="4">
                  <ConfirmPasswordInput
                    v-model="cpassword"
                    :password="password"
                    customHint=""
                    customPersistentHint="false"
                    customPlaceholder="Confirm Password"
                  />
                </v-col>
              </v-row>
            </v-stepper-window-item>
            <v-stepper-window-item :key="7" :value="7">
              <v-container>
                <v-card title="Form Preview" flat color="primary"> </v-card>
                <v-alert
                  type="info"
                  variant="tonal"
                  border="start"
                  border-color="primary"
                  text="This form is for preview only. An email notification will be sent to {{email}} with a link provided for printing of this form"
                  class="my-3"
                ></v-alert>
                <FormSheet
                  :last-name="lastName"
                  :first-name="firstName"
                  :middle-name="middleName"
                  :name-extension="nameExtension"
                  :sex="sex"
                  :Bdate="Bdate"
                  :nationality="nationality"
                  :religion="religion"
                  :height="height"
                  :weight="weight"
                  :address="address"
                  :email="email"
                  :phone-number="phoneNumber"
                  :course1="course1"
                  :station1="station1"
                  :course2="course2"
                  :station2="station2"
                  :course3="course3"
                  :station3="station3"
                  :getFileImageUrl="getFileImageUrl"
                />

                <v-container>
                  <v-row justify="end" class="text-right">
                    <v-col cols="1">
                      <v-btn
                        prepend-icon="mdi-restart"
                        variant="text"
                        height="100%"
                        @click="reset"
                        >Clear</v-btn
                      >
                    </v-col>
                    <v-col cols="2">
                      <v-btn
                        prepend-icon="mdi-content-save"
                        size="large"
                        color="success"
                        type="submit"
                        width="100%"
                        >Submit</v-btn
                      >
                    </v-col>
                    <v-col cols="1"> </v-col>
                  </v-row>
                </v-container>
              </v-container>
            </v-stepper-window-item>
          </v-form>
        </v-stepper-window>

        <v-stepper-actions
          :disable="disable"
          @click:prev="prevStep"
          @click:next="nextStep"
          color="primary"
        ></v-stepper-actions>
      </template>
    </v-stepper>
  </div>

  <!------dialogs-->
  <warning ref="warningDialog" />
  <warning ref="warningDialog2" text="Only 1 submission per mail" />
  <success
    ref="SuccessDialog"
    text="Application Submitted Successfully"
    @ok-clicked="reset"
  ></success>
</template>

<script>
import { getCourses } from "../../services/BackendApi";
import { getStations } from "../../services/BackendApi";
import FormSheet from "@/components/Application/FormSheet";
import axios from "axios";
export default {
  components: {
    FormSheet, // Register the FormSheet component
  },
  data() {
    return {
      agreed: null,
      lastName: "",
      firstName: "",
      middleName: "",
      nameExtension: "",
      sex: "",
      Bdate: null,
      nationality: null,
      religion: null,
      height: null,
      weight: null,
      address: "",
      email: "",
      password: "",
      cpassword: "",
      phoneNumber: "",
      selectedFile1: "",
      course1: null,
      station1: null,
      course2: null,
      station2: null,
      course3: null,
      station3: null,

      academicStatus: null,
      enrolledInPNP: null,
      firstTimeApplyPNP: null,

      fileConfig1: {
        labelText: "Choose your picture",
        placeholderText: "",
        icon: "mdi-camera",
        acceptedTypes: "image/png, image/jpeg",
        maxFileSize: 2000000,
        filetyperules: "PNG, JPEG, or BMP",
      },

      show: true,
      currentStep: ("currentStep", 1),
      steps: [
        {
          title: "Welcome",
          value: 1,
          complete: false,
          editable: false,
          color: "",
          rules: [],
        },
        {
          title: "Read First",
          value: 2,
          complete: false,
          editable: false,
          color: "",
          rules: [],
        },
        {
          title: "Confirmation",
          value: 3,
          complete: false,
          editable: false,
          color: "",
          rules: [],
          subtitle: "",
        },
        {
          title: "Personal",
          value: 4,
          complete: false,
          editable: false,
          color: "",
          rules: [],
          subtitle: "",
        },

        {
          title: "Program",
          value: 5,
          complete: false,
          editable: false,
          color: "",
          rules: [],
          subtitle: "",
        },
        {
          title: "Login Credentials",
          value: 6,
          complete: false,
          editable: false,
          color: "",
          rules: [],
          subtitle: "",
        },
        {
          title: "Submit",
          value: 7,
          complete: false,
          editable: false,
          color: "",
          rules: [],
          subtitle: "",
        },
      ],
    };
  },

  watch: {
    lastName(newVal) {
      localStorage.setItem("lastName", newVal);
    },
    firstName(newVal) {
      localStorage.setItem("firstName", newVal);
    },
    middleName(newVal) {
      localStorage.setItem("middleName", newVal);
    },
    nameExtension(newVal) {
      localStorage.setItem("nameExtension", newVal);
    },
    sex(newVal) {
      localStorage.setItem("sex", newVal);
    },
    Bdate(newVal) {
      localStorage.setItem("Bdate", newVal);
    },
    nationality(newVal) {
      localStorage.setItem("nationality", newVal);
    },
    religion(newVal) {
      localStorage.setItem("religion", newVal);
    },
    height(newVal) {
      localStorage.setItem("height", newVal);
    },
    weight(newVal) {
      localStorage.setItem("weight", newVal);
    },
    address(newVal) {
      localStorage.setItem("address", newVal);
    },
    email(newVal) {
      localStorage.setItem("email", newVal);
    },
    phoneNumber(newVal) {
      localStorage.setItem("phoneNumber", newVal);
    },
    course1(newVal) {
      localStorage.setItem("course1", newVal);
    },
    station1(newVal) {
      localStorage.setItem("station1", newVal);
    },
    course2(newVal) {
      localStorage.setItem("course2", newVal);
    },
    station2(newVal) {
      localStorage.setItem("station2", newVal);
    },
    course3(newVal) {
      localStorage.setItem("course3", newVal);
    },
    station3(newVal) {
      localStorage.setItem("station3", newVal);
    },
    academicStatus(newVal) {
      localStorage.setItem("academicStatus", newVal);
    },
    enrolledInPNP(newVal) {
      localStorage.setItem("enrolledInPNP", newVal);
    },
    firstTimeApplyPNP(newVal) {
      localStorage.setItem("firstTimeApplyPNP", newVal);
    },
  },
  created() {
    this.lastName = localStorage.getItem("lastName") || "";
    this.firstName = localStorage.getItem("firstName") || "";
    this.middleName = localStorage.getItem("middleName") || "";
    this.nameExtension = localStorage.getItem("nameExtension") || "";
    this.sex = localStorage.getItem("sex") || "";
    this.Bdate = localStorage.getItem("Bdate") || null;
    this.nationality = localStorage.getItem("nationality") || null;
    this.religion = localStorage.getItem("religion") || null;
    this.height = localStorage.getItem("height") || null;
    this.weight = localStorage.getItem("weight") || null;
    this.address = localStorage.getItem("address") || "";
    this.email = localStorage.getItem("email") || "";
    this.phoneNumber = localStorage.getItem("phoneNumber") || "";
    this.academicStatus = localStorage.getItem("academicStatus") || null;
    this.enrolledInPNP = localStorage.getItem("enrolledInPNP") || null;
    this.firstTimeApplyPNP = localStorage.getItem("firstTimeApplyPNP") || null;
    this.getCourses();
    this.getStations();
  },
  computed: {
    filteredStations1() {
      if (this.course1 && this.courses) {
        const selectedCourse = this.courses.find(
          (course) => course.Course_Name === this.course1
        );
        if (selectedCourse) {
          return selectedCourse.Stations_Offering.map(
            (offering) => offering.Station_Name
          );
        }
      }
      return [];
    },
    filteredStations2() {
      if (this.course2 && this.courses) {
        const selectedCourse = this.courses.find(
          (course) => course.Course_Name === this.course2
        );
        if (selectedCourse) {
          return selectedCourse.Stations_Offering.map(
            (offering) => offering.Station_Name
          );
        }
      }
      return [];
    },
    filteredStations3() {
      if (this.course2 && this.courses) {
        const selectedCourse = this.courses.find(
          (course) => course.Course_Name === this.course2
        );
        if (selectedCourse) {
          return selectedCourse.Stations_Offering.map(
            (offering) => offering.Station_Name
          );
        }
      }
      return [];
    },
  },

  methods: {
    getFileImageUrl() {
      if (
        this.$refs.FileInput &&
        this.$refs.FileInput.getObjectURL &&
        this.selectedFile1
      ) {
        // console.log(this.$refs.FileInput.getObjectURL(this.selectedFile1[0]));
        return this.$refs.FileInput.getObjectURL(this.selectedFile1[0]);
      } else {
        return require("@/assets/img/examples/no-image-icon-md.png");
      }
    },
    getFromLocalStorage(key, defaultValue) {
      const value = localStorage.getItem(key);
      return value ? JSON.parse(value) : defaultValue;
    },
    saveToLocalStorage(key, value) {
      localStorage.setItem(key, JSON.stringify(value));
    },

    handleCourseSelected1(selectedCourse) {
      this.course1 = selectedCourse;
      this.station1 = null;
    },
    handleStationSelected1(selectedStation) {
      this.station1 = selectedStation;
    },
    handleCourseSelected2(selectedCourse) {
      this.course2 = selectedCourse;
      this.station2 = null;
    },
    handleStationSelected2(selectedStation) {
      this.station2 = selectedStation;
    },
    handleCourseSelected3(selectedCourse) {
      this.course3 = selectedCourse;
      this.station3 = null;
    },
    handleStationSelected3(selectedStation) {
      this.station3 = selectedStation;
    },
    async nextStep() {
      const { valid } = await this.$refs.form.validate();

      // if (valid) alert("Form is valid");
      if (this.currentStep && valid) {
        for (let i = 0; i < this.currentStep; i++) {
          this.steps[i].rules = [(v) => true];
          this.steps[i].editable = true;
          this.steps[i].color = "success";
          this.steps[i].complete = true;
        }
        this.currentStep++;
      }
      if (this.currentStep) {
        this.steps[this.currentStep - 1].editable = true;
        if (!valid) {
          this.$refs.warningDialog.dialog = true;
          this.steps[this.currentStep - 1].rules = [(v) => false];
          this.steps[this.currentStep - 1].subtitle = "Missing Details";
        }
      }
      // this.currentStep++;
    },
    reset() {
      localStorage.clear(); // Clear all data from local storage
      window.location.reload(); // Reload the page
    },

    prevStep() {
      this.currentStep--;
    },
    async getCourses() {
      try {
        this.courses = await getCourses();
      } catch (error) {
        console.log(error);
      }
    },
    async getStations() {
      try {
        this.stations = await getStations();
      } catch (error) {
        console.log(error);
      }
    },
    async submitApplication() {
      try {
        const formData = new FormData();
        formData.append("lastName", this.lastName);
        formData.append("firstName", this.firstName);
        formData.append("middleName", this.middleName);
        formData.append("nameExtension", this.nameExtension);
        formData.append("sex", this.sex);
        formData.append("Bdate", this.Bdate);
        formData.append("nationality", this.nationality);
        formData.append("religion", this.religion);
        formData.append("height", this.height);
        formData.append("weight", this.weight);
        formData.append("address", this.address);
        formData.append("email", this.email);
        formData.append("password", this.password);
        formData.append("phoneNumber", this.phoneNumber);
        formData.append("selectedFile1", this.selectedFile1[0]);
        formData.append("course1", this.course1);
        formData.append("station1", this.station1);
        formData.append("course2", this.course2);
        formData.append("station2", this.station2);
        formData.append("course3", this.course3);
        formData.append("station3", this.station3);

        // Make the Axios POST request
        const respond = await axios.post("submitApplication", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        if (respond.data === true) {
          // Show a success alert or perform other success-related actions here
          this.$refs.SuccessDialog.dialog = true;
        } else {
          // Show the error Snackbar
          this.$refs.warningDialog.dialog = true;
        }
      } catch (error) {
        this.$refs.warningDialog2.dialog = true;
        console.log(error);
      }
    },
  },
};
</script>
