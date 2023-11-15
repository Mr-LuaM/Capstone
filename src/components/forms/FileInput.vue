<template>
  <v-file-input
    type="file"
    :rules="fileInputRules"
    show-size="1000"
    :accept="FileConfig.acceptedTypes"
    :placeholder="FileConfig.placeholderText"
    :append-inner-icon="FileConfig.icon"
    prepend-icon=""
    :label="FileConfig.labelText"
    variant="outlined"
    class="custom-file-input mt-3 pb-0"
    density="compact"
    v-model="selectedFile"
    @change="handleFileChange"
  ></v-file-input>
</template>

<script>
export default {
  name: "FileInput",
  props: {
    FileConfig: Object,
  },
  data() {
    return {
      selectedFile: "", // Local data property for selectedFile
      fileInputRules: [
        (value) => {
          if (!value || !value.length) {
            return "File is required";
          } else if (value[0] && value[0].size > this.FileConfig.maxFileSize) {
            return `File size should be less than ${
              this.FileConfig.maxFileSize / 1000
            } KB!`;
          } else if (
            value[0] &&
            !this.FileConfig.acceptedTypes.includes(value[0].type)
          ) {
            return "File type should be " + this.FileConfig.filetyperules;
          } else {
            return true;
          }
        },
      ],
    };
  },
  computed: {
    maxFileSizeKB() {
      return this.FileConfig.maxFileSize / 1000;
    },
  },
  methods: {
    getObjectURL(file) {
      if (file) {
        return URL.createObjectURL(file);
      }
      return null;
    },
    handleFileChange(event) {
      console.log("Selected File in handleFileChange:", event.target.files[0]);
      this.selectedFile = event.target.files;
    },
  },
};
</script>
