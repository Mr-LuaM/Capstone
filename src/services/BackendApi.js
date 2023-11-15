import axios from "axios";

export async function getCourses() {
  try {
    const courses = await axios.get("getCourses");
    return courses.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}

export async function getStations() {
  try {
    const courses = await axios.get("getStations");
    return courses.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}
export async function getStation() {
  try {
    const courses = await axios.get("getStation");
    return courses.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}

export async function getApplicants() {
  try {
    const applicants = await axios.get("getApplicants");
    return applicants.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}
