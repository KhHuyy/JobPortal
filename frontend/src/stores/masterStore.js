import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

const DEFAULT_CATEGORIES = [
  { id: 1, name: 'Software Engineering', slug: 'software-engineering', icon: 'bi-code-slash' },
  { id: 2, name: 'Data & AI', slug: 'data-ai', icon: 'bi-cpu' },
  { id: 3, name: 'Product & Design', slug: 'product-design', icon: 'bi-palette' },
  { id: 4, name: 'DevOps & Cloud', slug: 'devops-cloud', icon: 'bi-cloud-check' },
  { id: 5, name: 'QA & Testing', slug: 'qa-testing', icon: 'bi-shield-check' },
  { id: 6, name: 'Mobile Development', slug: 'mobile-development', icon: 'bi-phone' },
  { id: 7, name: 'Cybersecurity', slug: 'cybersecurity', icon: 'bi-lock' },
  { id: 8, name: 'Finance & Banking', slug: 'finance-banking', icon: 'bi-bank' },
]

const DEFAULT_SKILLS = [
  { id: 1, name: 'JavaScript', slug: 'javascript' },
  { id: 2, name: 'TypeScript', slug: 'typescript' },
  { id: 3, name: 'React', slug: 'react' },
  { id: 4, name: 'Vue.js', slug: 'vuejs' },
  { id: 5, name: 'Node.js', slug: 'nodejs' },
  { id: 6, name: 'Python', slug: 'python' },
  { id: 7, name: 'Java', slug: 'java' },
  { id: 8, name: 'Go', slug: 'go' },
  { id: 9, name: 'PHP', slug: 'php' },
  { id: 10, name: 'Laravel', slug: 'laravel' },
  { id: 11, name: 'Docker', slug: 'docker' },
  { id: 12, name: 'Kubernetes', slug: 'kubernetes' },
  { id: 13, name: 'AWS', slug: 'aws' },
  { id: 14, name: 'Azure', slug: 'azure' },
  { id: 15, name: 'GCP', slug: 'gcp' },
  { id: 16, name: 'PostgreSQL', slug: 'postgresql' },
  { id: 17, name: 'MySQL', slug: 'mysql' },
  { id: 18, name: 'MongoDB', slug: 'mongodb' },
  { id: 19, name: 'Redis', slug: 'redis' },
  { id: 20, name: 'Git', slug: 'git' },
  { id: 21, name: 'CI/CD', slug: 'cicd' },
  { id: 22, name: 'Agile', slug: 'agile' },
  { id: 23, name: 'Scrum', slug: 'scrum' },
  { id: 24, name: 'iOS', slug: 'ios' },
  { id: 25, name: 'Android', slug: 'android' },
  { id: 26, name: 'Flutter', slug: 'flutter' },
  { id: 27, name: 'React Native', slug: 'react-native' },
  { id: 28, name: 'Figma', slug: 'figma' },
  { id: 29, name: 'SQL', slug: 'sql' },
  { id: 30, name: 'Machine Learning', slug: 'machine-learning' },
  { id: 31, name: 'TensorFlow', slug: 'tensorflow' },
  { id: 32, name: 'PyTorch', slug: 'pytorch' },
  { id: 33, name: 'Spring Boot', slug: 'spring-boot' },
  { id: 34, name: '.NET', slug: 'net' },
  { id: 35, name: 'C#', slug: 'csharp' },
  { id: 36, name: 'C++', slug: 'cpp' },
  { id: 37, name: 'Rust', slug: 'rust' },
  { id: 38, name: 'GraphQL', slug: 'graphql' },
  { id: 39, name: 'REST API', slug: 'rest-api' },
  { id: 40, name: 'Terraform', slug: 'terraform' },
]

const DEFAULT_CITIES = ['Ho Chi Minh City', 'Hanoi', 'Da Nang', 'Can Tho', 'Remote']

export const useMasterStore = defineStore('master', () => {
  const categories = ref([...DEFAULT_CATEGORIES])
  const skills = ref([...DEFAULT_SKILLS])
  const cities = ref([...DEFAULT_CITIES])
  const loaded = ref(false)

  async function fetchMasterData() {
    if (loaded.value) return
    try {
      const res = await api.get('/master-data')
      if (res.data.data?.categories?.length) {
        categories.value = res.data.data.categories
      }
      if (res.data.data?.skills?.length) {
        skills.value = res.data.data.skills
      }
      if (res.data.data?.cities?.length) {
        cities.value = res.data.data.cities
      }
      loaded.value = true
    } catch (err) {
      console.warn('Backend API master-data unreachable, using verified fallback data.', err.message)
      // Keep DEFAULT_CATEGORIES and DEFAULT_CITIES intact
      loaded.value = true
    }
  }

  return { categories, skills, cities, loaded, fetchMasterData }
})


