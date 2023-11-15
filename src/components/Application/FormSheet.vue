<template>


  <v-sheet class="pa-10 mx-auto " border="lg opacity-20" width="80%"  >
    <v-row class="my-3">
      <!-- Left Section (School Logo) -->
      <v-col cols="3">
        <!-- Add your school logo or image here -->
        <v-img
          class="bg-white"
          width="100"
          height="100"
          :aspect-ratio="1"
          src="@/assets/img/logo/pnp-logo.png"
          alt="School Logo"
        />
      </v-col>

      <!-- Middle Section (PNP EDUCATION PROGRAM) -->
      <v-col cols="6" class="text-h5 font-weight-bold text-center"
        >PNP EDUCATION PROGRAM
        <br><span class="text-h6 ">
            APPLICATION FORM FOR COLLEGE ADMISSION
        </span>
      </v-col>

      <!-- Right Section (Application No) -->
      <v-col cols="3" class="text-h6 text-right">
        Application No: {{ applicationNo }}
      </v-col>
    </v-row>

    <v-divider class="border-opacity-100" ></v-divider>
 
    <v-row class="my-3">
      <!-- Left Part (Text) -->
      <v-col cols="6">
        <p class=" text-left">
        <b>To the Applicant</b><br />
        <ul>
            <li>Please read the Admission Guidelines and Requirements before accomplishing the form.</li>
            <li>This form is applicable for applicants who are applying for an education program for the first time without any previous college credits attained.</li>
            <li>Fill out this application form properly and completely.</li>
            <li>Write N/A on fields that are not applicable and do not leave any blank spaces.</li>
        </ul>
        </p>
      </v-col>

      <!-- Right Part (Student Image) -->
      <v-col cols="6" class="d-flex justify-end">
        <!-- Add the student's image here -->
        <v-img height="200" max-width="200" :src="selectedFile1 || imageSrc">
          <template v-slot:error>
            <v-img
              height="200"
              max-width="200"
              src="../../assets/img/examples/avatar_victor_metelskiy_shutterstock_548848999.jpg"
            ></v-img>
          </template>
        </v-img>
      </v-col>
    </v-row>
    <v-row class="my-3">
        <v-card text="Pursuant to Republic Act No. 10173, also known as the Data Privacy Act of 2012, the PNP recognizes its commitment to protect and respect the privacy of its customers and/or stakeholders and ensure that all information collected from them are all processed in accordance with the principles of transparency, legitimate purpose and proportionality mandated under the Data Privacy Act of 2012." variant="outlined"></v-card>
        </v-row>    
        <v-row class="my-3">
            <v-card class="font-weight-bold" width="100%" height="10%" variant="outlined" density="compact" compact>
                <v-card-text class="font-weight-bold pa-3" density="compact">APPLICANT’S INFORMATION</v-card-text>
            </v-card>        
         </v-row>   
         <v-row class="my-3 px-0 mt-0">
        <v-col cols="4" class="pl-0"  >
            <div>Last Name<span style="color:red">*</span></div>
          <v-text-field
            :value="lastName"
            readonly
            variant="outlined"
           density="compact"
           hide-details="auto"
          ></v-text-field>
        </v-col>
        <v-col cols="4" class="pl-0"  >
            <div>First Name<span style="color:red">*</span></div>
          <v-text-field
            :value="firstName"
            readonly
            variant="outlined"
           density="compact"
           hide-details="auto"
          ></v-text-field>
        </v-col>
       
        <v-col cols="2" class="pl-0">
            <div>Middle Name</div>
          <v-text-field
            :value="middleName"
            readonly
            variant="outlined"
           density="compact"
           hide-details="auto"
          ></v-text-field>
        </v-col>
        <v-col cols="2" class="pl-0">
            <div>Name Extension</div>
          <v-text-field
            :value="nameExtension"
            readonly
            variant="outlined"
           density="compact"
           hide-details="auto"
          ></v-text-field>
        </v-col>

          <!-- Sex -->
            <v-col cols="4" class="pl-0">
              <div>Sex<span style="color:red">*</span></div>
              <v-text-field
                :value="sex"
                readonly
                variant="outlined"
                density="compact"
                hide-details="auto"
              ></v-text-field>
            </v-col>

          <!-- Birth Date -->
          <v-col cols="4" class="pl-0">
            <div>Birth Date<span style="color:red">*</span></div>
            <v-text-field
              :value="Bdate"
              readonly
              variant="outlined"
              density="compact"
              hide-details="auto"
            ></v-text-field>
          </v-col>

          <!-- Nationality -->
          <v-col cols="4" class="pl-0">
            <div>Nationality<span style="color:red">*</span></div>
            <v-text-field
              :value="nationality"
              readonly
              variant="outlined"
              density="compact"
              hide-details="auto"
            ></v-text-field>
          </v-col>

          <!-- Religion -->
          <v-col cols="4" class="pl-0">
            <div>Religion<span style="color:red">*</span></div>
            <v-text-field
              :value="religion"
              readonly
              variant="outlined"
              density="compact"
              hide-details="auto"
            ></v-text-field>
          </v-col>



          <!-- Height -->
          <v-col cols="4" class="pl-0">
            <div>Height<span style="color:red">*</span></div>
            <v-text-field
              :value="height"
              readonly
              variant="outlined"
              density="compact"
              hide-details="auto"
              suffix="cm"
            ></v-text-field>
          </v-col>

          <!-- Weight -->
          <v-col cols="4" class="pl-0">
            <div>Weight<span style="color:red">*</span></div>
            <v-text-field
              :value="weight"
              readonly
              variant="outlined"
              density="compact"
              hide-details="auto"
              suffix="kg"
            ></v-text-field>
          </v-col>


          <!-- Address -->
          <v-col cols="12" class="pl-0">
            <div>Address<span style="color:red">*</span></div>
            <v-text-field
              :value="address"
              readonly
              variant="outlined"
              density="compact"
              hide-details="auto"

            ></v-text-field>
          </v-col>

          <!-- Email -->
          <v-col cols="6" class="pl-0">
            <div>Email<span style="color:red">*</span></div>
            <v-text-field
              :value="email"
              readonly
              variant="outlined"
              density="compact"
              hide-details="auto"

            ></v-text-field>
          </v-col>

          <!-- Phone Number -->
          <v-col cols="6" class="pl-0">
            <div>Phone Number<span style="color:red">*</span></div>
            <v-text-field
              :value="phoneNumber"
              readonly
              variant="outlined"
              density="compact"
              hide-details="auto"
            ></v-text-field>
          </v-col>
        </v-row>

          <v-row class="my-3">
                      <v-card class="font-weight-bold" width="100%" height="10%" variant="outlined" density="compact" compact>
                          <v-card-text class="font-weight-bold pa-3" density="compact">PROGRAM CHOICES / CHOSEN STATION</v-card-text>
                      </v-card>        
                  </v-row>  
                  <v-row class="my-3 px-0 mt-0">
                      <v-col cols="2" class="pl-0"  >
            <!-- Course 1 -->
            <v-card class="font-weight-bold" width="100%"  variant="flat" density="compact" >
                          <v-card-title class="font-weight-bold px-0 pt-5" >1. First Choice</v-card-title>
                      </v-card> 
            
            </v-col>
                  <v-col cols="5" class="pl-0"  >
            <!-- Course 1 -->
              <div>Course<span style="color:red">*</span></div>
              <v-text-field
                :value="course1"
                readonly
                variant="outlined"
                density="compact"
                hide-details="auto"
              ></v-text-field>
            </v-col>

            <!-- Station 1 -->
            <v-col cols="5" class="pl-0">
              <div>Station<span style="color:red">*</span></div>
              <v-text-field
                :value="station1"
                readonly
                variant="outlined"
                density="compact"
                hide-details="auto"
              ></v-text-field>
            </v-col>
            <v-col cols="2" class="pl-0"  >
            <!-- Course 1 -->
            <v-card class="font-weight-bold" width="100%"  variant="flat" density="compact" >
                          <v-card-title class="font-weight-bold px-0 pt-5" >2. Second Choice</v-card-title>
                      </v-card> 
            
            </v-col>
                  <v-col cols="5" class="pl-0"  >
            <!-- Course 1 -->
              <div>Course<span style="color:red">*</span></div>
              <v-text-field
                :value="course2"
                readonly
                variant="outlined"
                density="compact"
                hide-details="auto"
              ></v-text-field>
            </v-col>

            <!-- Station 1 -->
            <v-col cols="5" class="pl-0">
              <div>Station<span style="color:red">*</span></div>
              <v-text-field
                :value="station2"
                readonly
                variant="outlined"
                density="compact"
                hide-details="auto"
              ></v-text-field>
            </v-col>
            <v-col cols="2" class="pl-0"  >
            <!-- Course 1 -->
            <v-card class="font-weight-bold" width="100%"  variant="flat" density="compact" >
                          <v-card-title class="font-weight-bold px-0 pt-5" >3. Third Choice</v-card-title>
                      </v-card> 
            
            </v-col>
                  <v-col cols="5" class="pl-0"  >
            <!-- Course 1 -->
              <div>Course<span style="color:red">*</span></div>
              <v-text-field
                :value="course3"
                readonly
                variant="outlined"
                density="compact"
                hide-details="auto"
              ></v-text-field>
            </v-col>

            <!-- Station 1 -->
            <v-col cols="5" class="pl-0">
              <div>Station<span style="color:red">*</span></div>
              <v-text-field
                :value="station3"
                readonly
                variant="outlined"
                density="compact"
                hide-details="auto"
              ></v-text-field>
            </v-col>
          </v-row>
<v-divider class="border-opacity-100 " ></v-divider>
<v-row>
  <!-- Left Part (Text) -->
  <v-col cols="12" class="pt-5">
   <p>
      I hereby certify that all information provided herein is complete and
      accurate. Misrepresentation, falsification, and any form of dishonesty
      committed in relation to my application shall be sufficient grounds for
      my disqualification from admission to BatStateU.
    </p>

    
  </v-col>

  <!-- Right Part (Student Image) -->
  <v-col cols="12" class="d-flex text-right justify-end pt-10" >
   

    <p>
      Signature of applicant: _______________________<br />
      Date Signed: ________________
    </p>
  </v-col>
</v-row>



    


      
  </v-sheet>
</template>

<script>
export default {
    props: {
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
      phoneNumber: "",
      selectedFile1: "",
      course1: null,
      station1: null,
      course2: null,
      station2: null,
      course3: null,
      station3: null,
      getFileImageUrl: Function,

  },
 
  computed: {
    imageSrc() {
      const imageUrl = this.getFileImageUrl();
      return imageUrl !== null ? imageUrl : "";
    },
  },

};
</script>
