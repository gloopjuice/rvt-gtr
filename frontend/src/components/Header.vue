<template>
  <header class="header">
    <div class="container">
      <div class="header-content">
        <div class="logo">
          <RouterLink to="/home">RVTGTR</RouterLink>
        </div>
        
        <button class="mobile-menu-toggle" :class="{ 'active': mobileMenuOpen }" @click="toggleMobileMenu" aria-label="Toggle menu">
          <span class="bar"></span>
          <span class="bar"></span>
          <span class="bar"></span>
        </button>
        
        <div class="nav-container" :class="{ 'active': mobileMenuOpen }">
          <nav class="nav-menu">
            <RouterLink to="/home" class="nav-link" @click="closeMobileMenu">Mājas</RouterLink>
            <RouterLink to="/forums_hub" class="nav-link" @click="closeMobileMenu">Forums</RouterLink>
            <RouterLink to="/DownloadPage" class="nav-link" @click="closeMobileMenu">Lejupielādes</RouterLink>
            <RouterLink to="/profile" class="nav-link" v-if="profileData" @click="closeMobileMenu">
              <div class="profile-link">
                <span class="profile-icon">{{ getInitials(profileData.name) }}</span>
                <span class="profile-name">{{ profileData.name }}</span>
              </div>
            </RouterLink>
          </nav>
          
          <div class="header-actions">
            <button class="btn btn-secondary" @click="logout">Iziet</button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { RouterLink } from 'vue-router'
import axios from 'axios'

export default {
  name: "Header",
  data() {
    return {
      profileData: {},
      mobileMenuOpen: false
    };
  },
  mounted() {
    const authToken = localStorage.getItem('authToken');
    if (authToken) {
      this.fetchProfile(authToken);
    } else {
      this.$router.push('/login');
    }
    

    document.addEventListener('click', this.handleOutsideClick);
    
    
    window.addEventListener('resize', this.handleResize);
  },
  beforeUnmount() {
  
    document.removeEventListener('click', this.handleOutsideClick);
    window.removeEventListener('resize', this.handleResize);
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
        console.error(error.message);
      });
    },
    logout() {
      localStorage.removeItem('authToken');
      this.$router.push('/login');
    },
    getInitials(name) {
      if (!name) return '';
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase();
    },
    toggleMobileMenu(event) {
      event.stopPropagation();
      this.mobileMenuOpen = !this.mobileMenuOpen;
      
      
      if (this.mobileMenuOpen) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    },
    closeMobileMenu() {
      this.mobileMenuOpen = false;
      document.body.style.overflow = '';
    },
    handleOutsideClick(event) {
      const navContainer = document.querySelector('.nav-container');
      const mobileToggle = document.querySelector('.mobile-menu-toggle');
      
      if (this.mobileMenuOpen && 
          navContainer && 
          !navContainer.contains(event.target) &&
          !mobileToggle.contains(event.target)) {
        this.closeMobileMenu();
      }
    },
    handleResize() {
      if (window.innerWidth > 768 && this.mobileMenuOpen) {
        this.closeMobileMenu();
      }
    }
  },
  components: { RouterLink },
};
</script>

<style scoped>
.header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background-color: var(--color-surface);
  box-shadow: var(--shadow-md);
  z-index: 1000;
  height: 70px;
  display: block !important;
}

.container {
  height: 100%;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100%;
  position: relative;
}

.logo a {
  font-family: var(--font-family);
  font-size: var(--font-size-xl);
  font-weight: 700;
  color: var(--color-primary);
  text-decoration: none;
  letter-spacing: 1px;
  z-index: 110;
}

.nav-container {
  display: flex;
  align-items: center;
  gap: var(--space-xl);
}

.nav-menu {
  display: flex;
  align-items: center;
  gap: var(--space-lg);
}

.nav-link {
  color: var(--color-text-secondary);
  text-decoration: none;
  font-size: var(--font-size-md);
  font-weight: 500;
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--border-radius-sm);
  transition: all var(--transition-fast);
  white-space: nowrap;
}

.nav-link:hover, .nav-link.router-link-active {
  color: var(--color-primary);
}

.profile-link {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.profile-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background-color: var(--color-primary);
  color: var(--color-text-primary);
  border-radius: 50%;
  font-size: var(--font-size-sm);
  font-weight: 600;
}

.profile-name {
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.header-actions {
  display: flex;
  align-items: center;
}


.mobile-menu-toggle {
  display: none;
  flex-direction: column;
  justify-content: space-between;
  width: 30px;
  height: 21px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
  z-index: 110; 
}

.bar {
  height: 3px;
  width: 100%;
  background-color: var(--color-text-primary);
  border-radius: 10px;
  transition: all 0.3s ease-in-out;
}

@media (max-width: 768px) {
  .mobile-menu-toggle {
    display: flex;
  }
  
  .nav-container {
    position: fixed;
    top: 0;
    right: -100%;
    width: 80%;
    max-width: 300px;
    height: 100vh;
    background-color: var(--color-surface);
    box-shadow: var(--shadow-lg);
    flex-direction: column;
    justify-content: flex-start;
    padding: 100px var(--space-lg) var(--space-lg);
    transition: right 0.3s ease-in-out;
    z-index: 100;
    gap: var(--space-xxl);
    overflow-y: auto;
  }
  
  .nav-container.active {
    right: 0;
  }
  
  .nav-menu {
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
  }
  
  .nav-link {
    font-size: var(--font-size-lg);
    padding: var(--space-md) 0;
    width: 100%;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .profile-link {
    width: 100%;
    justify-content: flex-start;
  }
  
  .header-actions {
    width: 100%;
  }
  
  .header-actions .btn {
    width: 100%;
  }
  
  .mobile-menu-toggle.active .bar:nth-child(1) {
    transform: translateY(9px) rotate(45deg);
  }
  
  .mobile-menu-toggle.active .bar:nth-child(2) {
    opacity: 0;
  }
  
  .mobile-menu-toggle.active .bar:nth-child(3) {
    transform: translateY(-9px) rotate(-45deg);
  }
}
</style>
