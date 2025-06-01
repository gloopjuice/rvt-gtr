<template>
  <div class="edit-profile-page">
    <div class="container">
      <div class="edit-profile-card card">
        <h1 class="page-title">Rediģēt profilu</h1>
        
        <div class="profile-edit-form">
          <div class="avatar-section">
            <div class="avatar-container">
              <div class="avatar-placeholder" v-if="!profileData.avatar">
                <span>{{ getInitials(profileData.username) }}</span>
              </div>
              <img v-else :src="profileData.avatar" alt="Profile picture" class="avatar-image">
            </div>
          </div>
          
          <div class="form-section">
            <div class="form-control">
              <label for="username">Lietotājvārds</label>
              <input 
                type="text" 
                id="username" 
                class="form-input" 
                v-model="profileData.username" 
                placeholder="Ievadi savu lietotājvārdu"
              >
            </div>
            
            <div class="form-control">
              <label for="bio">Bio</label>
              <textarea 
                id="bio" 
                class="form-input bio-textarea" 
                v-model="profileData.bio" 
                placeholder="Ievadi savu bio"
              ></textarea>
            </div>

          </div>
        </div>
        
        <div class="action-buttons">
          <button class="btn btn-primary" @click="updateProfile" aria-label="save changes">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
              <polyline points="17 21 17 13 7 13 7 21"></polyline>
              <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            Saglabāt izmaiņas
          </button>
          
          <button v-if="profileData.role_id === 1" class="btn btn-secondary" @click="navigateToUserManagement">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            Pārvaldīt lietotājus
          </button>
          
          <button class="btn btn-danger" @click="confirmDelete">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              <line x1="10" y1="11" x2="10" y2="17"></line>
              <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
            Dzēst profilu
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: "EditProfileView",
  components: {
  },
  data() {
    return {
      profileData: {},
    };
  },
  mounted() {
    const authToken = localStorage.getItem('authToken');
    if (authToken) {
      this.fetchProfile(authToken);
    } else {
      this.$router.push('/login');
    }
  },
  methods: {
    fetchProfile(authToken){
      axios.get('/api/getUserProfile', {
        headers: {
          Authorization: `Bearer ${authToken}`
        }
      }).then((response) => {
        this.profileData = response.data.userData;
      }).catch((error) => {
        console.log(error.message);
      });
    },
    updateProfile() {
      const authToken = localStorage.getItem('authToken');
      if (!authToken) {
        console.error('Authentication token not found.');
        return;
      }
      axios.post('/api/updateProfile', {
        bio: this.profileData.bio,
        username: this.profileData.username
      }, {
        headers: {
          Authorization: `Bearer ${authToken}`
        }
      }).then(response => {
        alert("Izmaiņas saglabātas");
        this.$router.push('/profile');
      }).catch(error => {
        console.error(error);
      });
    },
    confirmDelete() {
      if (confirm('Vai tiešām vēlies dzēst savu profilu? Izmaiņas ir neatgriezeniskas!')) {
        this.deleteProfile();
      }
    },
    deleteProfile(){
      const authToken = localStorage.getItem('authToken');
      localStorage.removeItem('authToken');
      axios.delete('/api/deleteProfile').then(response => {
        console.log(response.data.message);
        alert('Profils veiksmīgi dzēsts');
        this.$router.push('/login');
      }).catch(error => {
        console.error('Error deleting profile:', error);
        alert('Kļūda profila dzēšanā!');
      });
    },
    navigateToUserManagement() {
      this.$router.push('/UserManagementView');
    },
    getInitials(name) {
      if (!name) return '';
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase();
    },

  },
};
</script>

<style scoped>
.edit-profile-page {
  min-height: 100vh;
  padding-top: 80px;
  background-color: var(--color-background);
}

.container {
  padding: var(--space-xl) var(--space-md);
  max-width: 800px;
  margin: 0 auto;
}

.edit-profile-card {
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  padding: var(--space-xl);
}

.page-title {
  font-size: var(--font-size-xxl);
  font-weight: 700;
  color: var(--color-text-primary);
  margin-bottom: var(--space-xl);
  text-align: center;
}

.profile-edit-form {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-xl);
  margin-bottom: var(--space-xl);
}

.avatar-section {
  flex: 0 0 200px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-md);
}

.avatar-container {
  width: 150px;
  height: 150px;
  margin-bottom: var(--space-md);
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  background-color: var(--color-primary-dark);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--font-size-xxl);
  font-weight: 600;
  color: var(--color-text-primary);
}

.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.upload-avatar-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
}

.form-section {
  flex: 1;
  min-width: 300px;
}

.bio-textarea {
  min-height: 150px;
  resize: vertical;
}

.action-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-md);
  justify-content: flex-end;
  margin-top: var(--space-xl);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding-top: var(--space-lg);
}

.action-buttons .btn {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.btn-danger {
  background-color: var(--color-error);
  color: var(--color-text-primary);
}

.btn-danger:hover {
  background-color: #d32f2f;
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.icon {
  width: 18px;
  height: 18px;
}

@media (max-width: 768px) {
  .profile-edit-form {
    flex-direction: column;
    align-items: center;
  }
  
  .avatar-section {
    margin-bottom: var(--space-lg);
  }
  
  .action-buttons {
    justify-content: center;
  }
  
  .action-buttons .btn {
    flex: 1 1 auto;
    min-width: 150px;
  }
}
</style>