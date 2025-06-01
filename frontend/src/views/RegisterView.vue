<template>
  <div class="register-page">
    <div class="register-container card">
      <div class="register-header">
        <h1 class="logo-text">RVTGTR</h1>
        <p class="subtitle">Izveidojiet savu kontu!</p>
      </div>
      
      <form @submit.prevent="register" class="register-form">
        <div class="form-control">
          <label for="email">E-pasts</label>
          <input 
            type="email" 
            id="email" 
            class="form-input" 
            placeholder="Ievadiet savu e-pastu" 
            v-model="formData.email" 
            required
          >
        </div>
        
        <div class="form-control">
          <label for="username">Lietotājvārds</label>
          <input 
            type="text" 
            id="username" 
            class="form-input" 
            placeholder="Ievadiet savu lietotājvārdu" 
            v-model="formData.username" 
            required
          >
        </div>
        
        <div class="form-control">
          <label for="name">Vārds</label>
          <input 
            type="text" 
            id="name" 
            class="form-input" 
            placeholder="Ievadiet savu vārdu" 
            v-model="formData.name" 
            required
          >
        </div>
        
        <div class="form-control">
          <label for="password">Parole</label>
          <input 
            type="password" 
            id="password" 
            class="form-input" 
            placeholder="Minimālais garums ir 7 simboli" 
            v-model="formData.password" 
            required 
            minlength="7"
          >
        </div>
        
        <div class="form-control">
          <label for="password_confirmation">Atkārtoti ievadiet savu paroli</label>
          <input 
            type="password" 
            id="password_confirmation" 
            class="form-input" 
            placeholder="Ievadiet savu paroli vēlreiz" 
            v-model="formData.password_confirmation" 
            required 
            minlength="7"
          >
        </div>
        
        <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
        
        <div class="form-actions">
          <button type="submit" class="btn btn-primary register-button">Izveidot kontu</button>
        </div>
      </form>
      
      <div class="register-footer">
        <p>Jau ir savs konts? <RouterLink to="/" class="login-link">Spied šeit!</RouterLink></p>
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
      formData: {
        email: "",
        username: "",
        name: "",
        password: "",
        password_confirmation: ""
      },
      errorMessage: ""
    };
  },
  methods: {
    async register() {
      try {
        const response = await axios.post('/api/register', this.formData);
        console.log(response.data);
        alert("Reģistrācija ir veiksmīga!");
        this.$router.push("/");
      } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
          this.errorMessage = Object.values(error.response.data.errors)[0][0];
        } else {
          this.errorMessage = "Reģistrācija nav izdevusies! Lūdzu pamēģiniet vēlāk.";
        }
        console.error(error.response.data);
        alert(this.errorMessage);
      }
    }
  }
};
</script>
<style scoped>
.register-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: var(--space-xl) var(--space-md);
  background-color: var(--color-background);
}

.register-container {
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  padding: var(--space-xl);
}

.register-header {
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

.register-form {
  margin-bottom: var(--space-lg);
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

.error-message {
  color: var(--color-error);
  font-size: var(--font-size-sm);
  margin-bottom: var(--space-md);
  padding: var(--space-sm);
  background-color: rgba(244, 67, 54, 0.1);
  border-radius: var(--border-radius-sm);
  border-left: 3px solid var(--color-error);
}

.form-actions {
  margin-top: var(--space-lg);
}

.register-button {
  width: 100%;
  padding: var(--space-md);
  font-weight: 600;
  font-size: var(--font-size-md);
  border-radius: var(--border-radius-md);
  transition: all var(--transition-fast);
}

.register-footer {
  text-align: center;
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
}

.login-link {
  color: var(--color-primary);
  font-weight: 500;
  text-decoration: none;
}

.login-link:hover {
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
  .register-container {
    padding: var(--space-lg);
  }
  
  .logo-text {
    font-size: var(--font-size-xxl);
  }
}
</style>
