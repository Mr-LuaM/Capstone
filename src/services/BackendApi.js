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
    const stations = await axios.get("getStations");
    return stations.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}
export async function getStation() {
  try {
    const station = await axios.get("getStation");
    return station.data; // Return the data
  } catch (error) {
    console.log(error);
    throw error; // Rethrow the error
  }
}
export async function getCourse() {
  try {
    const course = await axios.get("getCourse");
    return course.data; // Return the data
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
