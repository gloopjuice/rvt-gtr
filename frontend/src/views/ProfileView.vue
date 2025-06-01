<template>
  <div class="profile-page">
    <div class="container profile-container">
      <div class="profile-card card">
        <div class="profile-header">
          <div class="profile-avatar">
            <div class="avatar-placeholder" v-if="!profileData.avatar">
              <span>{{ getInitials(profileData.username) }}</span>
            </div>
            <img v-else :src="profileData.avatar" alt="Profile picture" class="avatar-image">
          </div>
          
          <div class="profile-info">
            <h1 class="profile-username" v-if="profileData">{{ profileData.username }}</h1>
            <p class="profile-name" v-if="profileData.name">{{ profileData.name }}</p>
            <p class="profile-email" v-if="profileData.email">{{ profileData.email }}</p>
          </div>
        </div>
        
        <div class="profile-content">
          <div class="profile-section">
            <h2 class="section-title">Bio</h2>
            <p class="bio" v-if="profileData && profileData.bio">{{ profileData.bio }}</p>
            <p class="bio-empty" v-else>Tavs bio ir tukšs!</p>
          </div>
          
          <div class="profile-actions">
            <button class="btn btn-primary" @click="editProfile" aria-label="edit profile">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M9.65661 17L6.99975 17L6.99975 14M6.10235 14.8974L17.4107 3.58902C18.1918 2.80797 19.4581 2.80797 20.2392 3.58902C21.0202 4.37007 21.0202 5.6364 20.2392 6.41745L8.764 17.8926C8.22794 18.4287 7.95992 18.6967 7.6632 18.9271C7.39965 19.1318 7.11947 19.3142 6.8256 19.4723C6.49475 19.6503 6.14115 19.7868 5.43395 20.0599L3 20.9998L3.78312 18.6501C4.05039 17.8483 4.18403 17.4473 4.3699 17.0729C4.53497 16.7404 4.73054 16.424 4.95409 16.1276C5.20582 15.7939 5.50466 15.4951 6.10235 14.8974Z"/>
              </svg>
              Rediģēt profilu
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: "ProfileView",
  data() {
    return {
      profileData: {}
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
    fetchProfile(authToken) {
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
    editProfile() {
      this.$router.push('/EditProfileView');
    },
    getInitials(name) {
      if (!name) return '';
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase();
    }
  },
  components: {
  },
};
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  padding-top: 80px;
  background-color: var(--color-background);
}

.profile-container {
  padding: var(--space-xl) var(--space-md);
  max-width: 900px;
  margin: 0 auto;
}

.profile-card {
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
}

.profile-header {
  display: flex;
  align-items: center;
  padding: var(--space-xl);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  gap: var(--space-lg);
}

.profile-avatar {
  flex-shrink: 0;
}

.avatar-placeholder {
  width: 120px;
  height: 120px;
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
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-info {
  flex-grow: 1;
}

.profile-username {
  font-size: var(--font-size-xxl);
  font-weight: 700;
  color: var(--color-text-primary);
  margin-bottom: var(--space-xs);
}

.profile-name {
  font-size: var(--font-size-lg);
  color: var(--color-text-secondary);
  margin-bottom: var(--space-xs);
}

.profile-email {
  font-size: var(--font-size-md);
  color: var(--color-text-muted);
}

.profile-content {
  padding: var(--space-xl);
}

.profile-section {
  margin-bottom: var(--space-xl);
}

.section-title {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--color-text-secondary);
  margin-bottom: var(--space-md);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  padding-bottom: var(--space-sm);
}

.bio {
  font-size: var(--font-size-md);
  line-height: 1.6;
  color: var(--color-text-primary);
}

.bio-empty {
  font-size: var(--font-size-md);
  color: var(--color-text-muted);
  font-style: italic;
}

.profile-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: var(--space-lg);
}

.profile-actions .btn {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-sm) var(--space-lg);
}

.icon {
  width: 18px;
  height: 18px;
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
    padding: var(--space-lg) var(--space-md);
  }
  
  .profile-avatar {
    margin-bottom: var(--space-md);
  }
  
  .avatar-placeholder, .avatar-image {
    width: 100px;
    height: 100px;
    margin: 0 auto;
  }
  
  .profile-username {
    font-size: var(--font-size-xl);
  }
  
  .profile-content {
    padding: var(--space-lg) var(--space-md);
  }
  
  .profile-actions {
    justify-content: center;
  }
}

  .edit-button svg {
    width: 1.5rem; /* Reduced icon size */
    height: 1.5rem; /* Reduced icon size */
  }
</style>
