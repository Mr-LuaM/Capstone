<template>
  <v-card color="basil" variant="flat">
    <v-card-title class="text-center justify-center py-6 bg-secondary">
      <h1 class="font-weight-bold text-h2 text-basil">{{ examTitle }}</h1>
    </v-card-title>

    <v-tabs
      v-model="tab"
      bg-color="transparent"
      color="basil"
      align-tabs="center"
    >
      <v-tab value="question">Questions</v-tab>
      <v-tab value="responses">Responses</v-tab>
    </v-tabs>

    <v-card-text>
      <v-window v-model="tab">
        <v-window-item value="question">
          <!-- Static Form Elements Here -->

          <v-card
            align="center"
            variant="outlined"
            class="text-left mx-auto pa-4 mb-6"
            max-width="700px"
          >
            <v-card-title>
              <GenericTextField
                custom-variant="underlined"
                customDensity="default"
                customLabel="Title"
                v-model="examTitle"
            /></v-card-title>
            <v-card-subtitle>description</v-card-subtitle>
            <v-card-title>
              <EmailInput :showTooltip="false" disabled customLabel="Email" />
              <v-card-subtitle>this form is collecting email</v-card-subtitle>
            </v-card-title>
          </v-card>
          <v-form ref="form" @submit.prevent="submit">
            <!-- Dynamic Questions -->
            <div class="mx-auto mb-6" style="max-width: 700px">
              <div v-for="(question, qIndex) in questions" :key="qIndex">
                <v-card
                  align="center"
                  variant="outlined"
                  class="text-left mx-auto pa-4 mb-6"
                  max-width="700px"
                >
                  <v-card-title>
                    <v-row align="center" no-gutters>
                      <v-col cols="11">
                        <GenericTextField
                          custom-label="Question"
                          v-model="question.text"
                          custom-density="default"
                          custom-variant="solo-inverted"
                          class="mr-3"
                        />
                      </v-col>
                      <v-col cols="1">
                        <v-btn
                          class="mt-n1"
                          small
                          color="error"
                          @click="removeQuestion(qIndex)"
                        >
                          <v-icon>mdi-close</v-icon>
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-card-title>
                  <v-card-text>
                    <div
                      v-for="(option, oIndex) in question.options"
                      :key="oIndex"
                      class="mb-2"
                    >
                      <v-row no-gutters>
                        <v-col cols="11">
                          <GenericTextField
                            custom-label="Option"
                            v-model="question.options[oIndex]"
                          />
                        </v-col>
                        <v-col cols="1">
                          <v-btn
                            variant="plain"
                            class="mt-0"
                            small
                            color="error"
                            @click="removeOption(qIndex, oIndex)"
                          >
                            <v-icon>mdi-close</v-icon>
                          </v-btn>
                        </v-col>
                      </v-row>
                    </div>

                    <div class="d-flex justify-start mt-2">
                      <v-btn @click="() => addOption(qIndex)" color="success">
                        <v-icon>mdi-plus</v-icon>
                      </v-btn>
                    </div>
                    <div class="d-flex justify-start mt-6">
                      <v-autocomplete
                        :items="question.options"
                        v-model="question.correctAnswer"
                        variant="solo-inverted"
                        label="Correct Answer"
                        :rules="[(v) => !!v || 'Correct answer is required']"
                        return-object
                      ></v-autocomplete>
                    </div>
                  </v-card-text>
                </v-card>
              </div>
              <div class="d-flex justify-end">
                <v-btn @click="addQuestion" color="secondary" class="mb-4"
                  >Add Question</v-btn
                >
              </div>
            </div>

            <!-- Static Form Elements Here -->
            <v-card
              align="center"
              variant="outlined"
              class="text-left mx-auto pa-4 mb-6"
              max-width="700px"
            >
              <v-card-title>
                <v-row align="center" justify="start">
                  <v-col cols="12" md="4">
                    <GenericNumber
                      custom-label="Duration(m)"
                      v-model="examDuration"
                    />
                  </v-col>
                  <v-col cols="12" md="4">
                    <GenericDate
                      labelText="Start Date"
                      v-model="examStartDate"
                    />
                  </v-col>
                  <v-col cols="12" md="4">
                    <GenericDate labelText="Stop Date" v-model="examEndDate" />
                  </v-col>
                </v-row>
              </v-card-title>
            </v-card>

            <!-- Save and Cancel Buttons -->
            <div class="mx-auto mb-6" style="max-width: 700px">
              <div class="d-flex justify-end">
                <v-btn class="mr-4" color="error" @click="cancel">Cancel</v-btn>
                <v-btn color="success" @click="saveForm">Save Form</v-btn>
              </div>
            </div>
          </v-form>
        </v-window-item>

        <v-window-item value="responses">
          <v-card>
            <v-card-title> Exam Responses </v-card-title>
            <v-data-table
              :headers="headers"
              :items="responses"
              :items-per-page="5"
              class="elevation-1"
            >
              <template v-slot:item.name="{ item }">
                {{ item.studentFirstName }} {{ item.studentLastName }}
              </template>
            </v-data-table>
          </v-card></v-window-item
        >
      </v-window>
    </v-card-text>
  </v-card>

  <infoSnack ref="snackbar" />
  <confirmationModal
    ref="confirmationModal"
    @confirm="archiveItem"
    @cancel="this.$refs.confirmationModal.dialog = false"
  />
</template>

<script>
import router from "@/router";
import axios from "axios";
export default {
  data: () => ({
    isEditMode: false,
    examId: null,
    tab: null,
    questions: [{ text: "", options: [""], correctAnswer: "" }],
    examDuration: "",
    examStartDate: "",
    examEndDate: "", // Initialized with one empty question for a new form
    examTitle: "Untitled",
    headers: [
      { title: "Student Name", value: "name" },
      { title: "Assignment Status", value: "Assignment_Status" },
      { title: "Completion Time", value: "Completion_Time" },
      { title: "Score", value: "Score" },
    ],
    responses: [],
  }),
  methods: {
    addQuestion() {
      this.questions.push({ text: "", options: [""] });
    },
    addOption(questionIndex) {
      this.questions[questionIndex].options.push("");
    },
    async saveForm() {
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        // Open the confirm dialog for approving or rejecting an application
        this.$refs.confirmationModal.dialog = true;
        this.$refs.confirmationModal.confirmAction = () =>
          this.saveFormExecute();
      } else {
        this.$refs.snackbar.openSnackbar("Validation error", "error");
        this.$refs.confirmationModal.dialog = false;
      }
    },
    async saveFormExecute() {
      try {
        const formData = new FormData();
        const userID = this.$store.state.userId;
        // Append relevant form data
        formData.append("examTitle", this.examTitle);
        formData.append("questions", JSON.stringify(this.questions));
        formData.append("duration", this.examDuration);
        formData.append("startDate", this.examStartDate);
        formData.append("endDate", this.examEndDate);

        // Append userId from the store
        formData.append("teacherId", userID);

        if (this.isEditMode && this.examId) {
          formData.append("examId", this.examId);
        }
        // ...

        const response = await axios.post("addExam", formData);

        if (response.data.success) {
          this.$refs.snackbar.openSnackbar(response.data.message, "success");
          this.$refs.confirmationModal.dialog = false;
          // Additional actions after successful save, e.g., redirecting or updating UI
          router.replace({ path: "/teacher/exams" });
        } else {
          this.$refs.snackbar.openSnackbar(response.data.message, "error");
          console.error("Failed to save form:", response.data.error);
          this.$refs.confirmationModal.dialog = false;
        }
      } catch (error) {
        this.$refs.snackbar.openSnackbar("ERROR", "error");
        console.error("Error while saving form:", error);
        this.$refs.confirmationModal.dialog = false;
      }
    },
    cancel() {
      // Implement logic to handle cancel action
      // Implement logic to handle cancel action
      router.replace({ path: "/teacher/exams" });
    },
    removeOption(questionIndex, optionIndex) {
      if (this.questions[questionIndex].options.length > 1) {
        this.questions[questionIndex].options.splice(optionIndex, 1);
      } else {
        this.$refs.snackbar.openSnackbar(
          "Each question must have at least one option.",
          "error"
        );
      }
    },
    removeQuestion(index) {
      if (this.questions.length > 1) {
        this.questions.splice(index, 1);
      } else {
        this.$refs.snackbar.openSnackbar(
          "Each form must have at least one question.",
          "error"
        );
      }
    },
    async fetchExamData(examId) {
      try {
        const response = await axios.get(`/getExam/${examId}`);
        this.populateFormData(response.data);
      } catch (error) {
        console.error("Error fetching exam data:", error);
        // Handle error
      }
    },
    async fetchResponses(examId) {
      try {
        const response = await axios.get(`/getResponse/${examId}`);
        this.responses = response.data.map((resp) => ({
          ...resp,
          name: resp.studentFirstName + " " + resp.studentLastName,
        }));
      } catch (error) {
        console.error("Error fetching responses:", error);
        // Handle error (e.g., show notification)
      }
    },
    populateFormData(data) {
      this.examTitle = data.Exam_Title;
      this.examDuration = data.Duration_Minutes;
      this.examStartDate = data.Start_Time.split(" ")[0]; // Format as 'YYYY-MM-DD'
      this.examEndDate = data.End_Time.split(" ")[0]; // Format as 'YYYY-MM-DD'

      this.questions = data.questions.map((question) => ({
        Question_ID: question.Question_ID, // Include Question_ID
        text: question.Question_Text,
        correctAnswer: question.Correct_Answer,
        options: question.options,
      }));
    },

    // Optionally, add other methods for editing and initializing existing data
  },
  created() {
    if (this.$route.params.id) {
      this.examId = atob(this.$route.params.id); // Base64 decode the ID
      this.isEditMode = true;
      this.fetchExamData(this.examId);
      this.fetchResponses(this.examId);
    }
  },
  // Optionally, add lifecycle hooks like created or mounted to initialize form data if editing
};
</script>

<style>
/* Add your CSS styles if needed */
</style>
