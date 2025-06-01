<template>
  <div class="login-page">
    <div class="login-container card">
      <div class="login-header">
        <h1 class="logo-text">RVTGTR</h1>
        <p class="subtitle">Ievadi savu profila informāciju!</p>
      </div>
      
      <form @submit.prevent="login" class="login-form">
        <div class="form-control">
          <label for="email">E-pasts</label>
          <input 
            type="email" 
            id="email" 
            class="form-input" 
            placeholder="Ievadiet savu e-pastu" 
            v-model="email" 
            required
          >
        </div>
        
        <div class="form-control">
          <div class="password-header">
            <label for="password">Parole</label>
          </div>
          <input 
            type="password" 
            id="password" 
            class="form-input" 
            placeholder="Ievadiet savu paroli"
            v-model="password" 
            required
          >
        </div>
        
        <div v-if="showError" class="error-message">
          {{ errorMessage }}
        </div>

        <div class="forgot-password">
          <RouterLink to="/ForgotPass" class="forgot-password">Aizmirsi paroli?</RouterLink>
        </div>
        
        <div class="form-actions">
          <button type="submit" class="btn btn-primary login-button">Ienākt</button>
        </div>
      </form>
      
      <div class="login-footer">
        <p>Neesi izveidojis kontu? <RouterLink to="/register" class="register-link">Spied šeit!</RouterLink></p>
      </div>
    </div>
    
    <footer class="site-footer">
      <div class="container">
        <p>© 2025 RVTGTR</p>
      </div>
    </footer>
  </div>
</template>


<script>
import axios from 'axios';


export default {
  data() {
    return {
      email: '',
      password: '',
      showError: false,
      errorMessage: '',
    };
  },
  methods: {
    async login() {
      try {
        const response = await axios.post("/api/login", {
          email: this.email,
          password: this.password
        });
        console.log(response.data);
        if (response.status === 200) {
          if (response.data.status) {
            localStorage.setItem('authToken', response.data.token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
            this.$router.push('/Home');
          } else {
            console.error('login failed', response.data.message);
            this.errorMessage = response.data.message;
            this.showError = true;
          }
        } else {
          console.error('unexpected response status', response.status);
          this.errorMessage = 'An unexpected error occurred. Please try again later.';
          this.showError = true;
        }
      } catch (error) {
        console.error(error.response?.data || error);
        this.errorMessage = error.response?.data?.message || 'Login failed. Please check your credentials.';
        this.showError = true;
      }
    },
  },
};
</script>


<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: var(--space-xl) var(--space-md);
  background-color: var(--color-background);
}

.login-container {
  width: 100%;
  max-width: 450px;
  margin: 0 auto;
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  padding: var(--space-xl);
}

.login-header {
  text-align: center;
  margin-bottom: var(--space-xl);
}

.logo-text {
  font-size: var(--font-size-xxxl);
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--space-xs);
  letter-spacing: 1px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.subtitle {
  color: var(--color-text-secondary);
  font-size: var(--font-size-md);
}

.login-form {
  margin-bottom: var(--space-lg);
}

.error-message {
  background-color: rgba(255, 87, 87, 0.1);
  border-left: 3px solid var(--color-danger, #ff5757);
  color: var(--color-danger, #ff5757);
  padding: var(--space-sm);
  margin: var(--space-sm) 0;
  border-radius: var(--border-radius-sm);
  font-size: var(--font-size-sm);
}

.form-control {
  margin-bottom: var(--space-md);
}

.form-control label {
  display: block;
  margin-bottom: var(--space-xs);
  font-weight: 500;
  color: var(--color-text-secondary);
}

.password-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-xs);
}

.forgot-password {
  font-size: var(--font-size-sm);
  color: var(--color-primary);
  text-decoration: none;
}

.forgot-password:hover {
  text-decoration: underline;
}

.form-actions {
  margin-top: var(--space-lg);
}

.login-button {
  width: 100%;
  padding: var(--space-md);
  font-weight: 600;
  font-size: var(--font-size-md);
  border-radius: var(--border-radius-md);
  transition: all var(--transition-fast);
}

.login-footer {
  text-align: center;
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
}

.register-link {
  color: var(--color-primary);
  font-weight: 500;
  text-decoration: none;
}

.register-link:hover {
  text-decoration: underline;
}

.site-footer {
  margin-top: auto;
  padding: var(--space-lg) 0;
  text-align: center;
  color: var(--color-text-muted);
  font-size: var(--font-size-sm);
  width: 100%;
}

@media (max-width: 768px) {
  .login-container {
    padding: var(--space-lg);
  }
  
  .logo-text {
    font-size: var(--font-size-xxl);
  }
}
</style>




