<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

defineOptions({ layout: AuthLayout })

const props = defineProps({
  teams: { type: Array, default: () => [] },
  jobRoles: { type: Array, default: () => [] },
})

const form = useForm({
  name: '',
  email: '',
  team_id: '',
  role: '',
  password: '',
  password_confirmation: '',
})

const submit = () => form.post('/register')
</script>

<template>
  <div class="register-container">
    <div class="register-wrapper">
      <!-- Left Panel: Branding & Information -->
      <div class="left-panel">
        <div class="left-panel-content">
          <!-- Logo -->
          <div class="logo-section">
            <img 
              src="/images/Kedebah Logo.png" 
              alt="KEDEBAH ERP" 
              class="brand-logo"
            />
          </div>
          
          <!-- Main Heading -->
          <h1 class="main-heading">Join Our Team</h1>
          
          <!-- Subheading -->
          <p class="subheading">
            Create your account and become part of a collaborative team 
            working on exciting projects with powerful tools and real-time updates.
          </p>
          
          <!-- Illustration Placeholder -->
          <div class="illustration-space">
            <!-- Space reserved for illustration image -->
            <img 
              src="/images/media-86.png" 
              alt="Team Collaboration Illustration" 
              class="illustration-image"
            />
          </div>
          
          <!-- Features Section -->
          <div class="features-section">
            <!-- Feature 1 -->
            <div class="feature-item">
              <h3 class="feature-heading">Team Collaboration</h3>
              <p class="feature-text">
                <span class="bullet-marker">•</span> Work together seamlessly
              </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-item">
              <h3 class="feature-heading">Project Management</h3>
              <p class="feature-text">
                <span class="bullet-marker">•</span> Track tasks efficiently
              </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-item">
              <h3 class="feature-heading">Instant Updates</h3>
              <p class="feature-text">
                <span class="bullet-marker">•</span> Stay informed in real-time
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Register Form -->
      <div class="right-panel">
        <div class="form-container">
          <!-- Header -->
          <div class="form-header">
            <h2 class="form-title">Create Your Account</h2>
            <p class="form-subtitle">
              Create your team-member account and start working on assigned projects.
            </p>
          </div>

          <!-- Register Form -->
          <form class="register-form" @submit.prevent="submit">
            <!-- Full Name -->
            <div class="form-group">
              <label for="name" class="form-label">Full Name</label>
              <input 
                id="name" 
                v-model="form.name" 
                type="text" 
                class="form-input" 
                placeholder="Enter your full name"
                autocomplete="name" 
                required
              />
              <p v-if="form.errors.name" class="error-text">{{ form.errors.name }}</p>
            </div>

            <!-- Email -->
            <div class="form-group">
              <label for="email" class="form-label">Email Address</label>
              <input 
                id="email" 
                v-model="form.email" 
                type="email" 
                class="form-input"
                placeholder="you@example.com" 
                autocomplete="email" 
                required
              />
              <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
            </div>

            <!-- Team Selection -->
            <div class="form-group">
              <label for="team_id" class="form-label">Team</label>
              <select id="team_id" v-model="form.team_id" class="form-input" required>
                <option value="" disabled>Select a team</option>
                <option v-for="team in props.teams" :key="team.id" :value="team.id">
                  {{ team.name }}
                </option>
              </select>
              <p v-if="form.errors.team_id" class="error-text">{{ form.errors.team_id }}</p>
            </div>

            <!-- Role Selection -->
            <div class="form-group">
              <label for="role" class="form-label">Role</label>
              <select id="role" v-model="form.role" class="form-input" required>
                <option value="" disabled>Select a role</option>
                <option v-for="jobRole in props.jobRoles" :key="jobRole.value" :value="jobRole.value">
                  {{ jobRole.label }}
                </option>
              </select>
              <p v-if="form.errors.role" class="error-text">{{ form.errors.role }}</p>
            </div>

            <!-- Password -->
            <div class="form-group">
              <label for="password" class="form-label">Password</label>
              <input 
                id="password" 
                v-model="form.password" 
                type="password" 
                class="form-input"
                placeholder="Create a strong password" 
                autocomplete="new-password" 
                required
              />
              <p v-if="form.errors.password" class="error-text">{{ form.errors.password }}</p>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
              <label for="password_confirmation" class="form-label">Confirm Password</label>
              <input 
                id="password_confirmation" 
                v-model="form.password_confirmation" 
                type="password" 
                class="form-input"
                placeholder="Confirm your password" 
                autocomplete="new-password" 
                required
              />
            </div>

            <!-- Submit Button -->
            <button 
              type="submit" 
              class="submit-button" 
              :disabled="form.processing"
            >
              <i class="ri-user-add-line mr-2"></i>
              {{ form.processing ? 'Creating Account...' : 'Create Account' }}
            </button>
          </form>

          <!-- Sign In Link -->
          <div class="signin-section">
            <p class="signin-text">
              Already registered?
              <Link href="/login" class="signin-link">Sign in</Link>
            </p>
          </div>

          <!-- Additional Info -->
          <div class="terms-section">
            <p class="terms-text">
              By creating an account, you agree to our 
              <a href="#" class="terms-link">Terms</a>
              and 
              <a href="#" class="terms-link">Privacy Policy</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="register-footer">
      <p class="footer-text">© 2026 KEDEBAH ERP. All rights reserved.</p>
    </footer>
  </div>
</template>

<style scoped>
/* ===========================
   Two-Panel Register Layout
   =========================== */

/* Main Container */
.register-container {
  width: 100%;
  min-height: 100vh;
  height: 100%;
  display: flex;
  flex-direction: column;
  background: transparent;
  margin: 0;
  padding: 0;
}

/* Wrapper for Two Panels */
.register-wrapper {
  flex: 1;
  display: flex;
  flex-direction: row;
  width: 100%;
  min-height: calc(100vh - 60px);
  margin: 0;
  padding: 0;
}

/* ===========================
   LEFT PANEL - Branding
   =========================== */
.left-panel {
  flex: 1;
  width: 50%;
  background: linear-gradient(135deg, #0f766e 0%, #047857 50%, #065f46 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 6%;
  position: relative;
  overflow: hidden;
}

.left-panel::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  pointer-events: none;
}

.left-panel-content {
  width: 100%;
  max-width: 700px;
  z-index: 1;
  position: relative;
}

/* Logo Section */
.logo-section {
  margin-bottom: 3rem;
}

.brand-logo {
  height: 48px;
  width: auto;
  filter: brightness(0) invert(1);
}

/* Main Heading - Serif Font */
.main-heading {
  font-family: Georgia, 'Times New Roman', Times, serif;
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  color: #ffffff;
  letter-spacing: -0.02em;
}

/* Subheading - Sans-serif */
.subheading {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  font-size: 1.125rem;
  line-height: 1.7;
  color: #d1fae5;
  margin-bottom: 3rem;
  font-weight: 400;
}

/* Illustration Space */
.illustration-space {
  margin: 3rem 0;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.15;
}

.illustration-image {
  max-width: 100%;
  height: auto;
  object-fit: contain;
}

/* Features Section */
.features-section {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  margin-top: 2rem;
}

.feature-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

/* Feature Heading - Bold Sans-serif */
.feature-heading {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  color: #ffffff;
  margin: 0;
}

/* Feature Text with Custom Bullet */
.feature-text {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  font-size: 0.95rem;
  color: #d1fae5;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
  padding-left: 0.25rem;
}

.bullet-marker {
  color: #6ee7b7;
  font-size: 1.5rem;
  line-height: 1;
}

/* ===========================
   RIGHT PANEL - Register Form
   =========================== */
.right-panel {
  flex: 1;
  width: 50%;
  background: #1f2937;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 6%;
  overflow-y: auto;
}

.form-container {
  width: 100%;
  max-width: 520px;
}

/* Form Header */
.form-header {
  margin-bottom: 2rem;
  text-align: left;
}

.form-title {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  font-size: 2rem;
  font-weight: 700;
  color: #f9fafb;
  margin-bottom: 0.5rem;
}

.form-subtitle {
  font-size: 1rem;
  color: #d1d5db;
  font-weight: 400;
}

/* Register Form */
.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

/* Form Group */
.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #e5e7eb;
  margin: 0;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  color: #f9fafb;
  background: #374151;
  border: 1px solid #4b5563;
  border-radius: 0.5rem;
  outline: none;
  transition: all 0.2s;
}

.form-input::placeholder {
  color: #9ca3af;
}

.form-input:focus {
  border-color: #059669;
  background: #2d3748;
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.form-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Select Styling */
select.form-input {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}

/* Error Text */
.error-text {
  font-size: 0.875rem;
  color: #f87171;
  margin: 0;
}

/* Submit Button */
.submit-button {
  width: 100%;
  padding: 0.875rem 1.5rem;
  font-size: 1rem;
  font-weight: 600;
  color: #ffffff;
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  border: none;
  border-radius: 0.75rem;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.submit-button:hover:not(:disabled) {
  background: linear-gradient(135deg, #047857 0%, #065f46 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.submit-button:active:not(:disabled) {
  transform: translateY(0);
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Sign In Section */
.signin-section {
  text-align: center;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #374151;
}

.signin-text {
  font-size: 0.875rem;
  color: #d1d5db;
  margin: 0;
}

.signin-link {
  color: #6ee7b7;
  text-decoration: none;
  font-weight: 500;
  margin-left: 0.25rem;
  transition: color 0.2s;
}

.signin-link:hover {
  color: #a7f3d0;
  text-decoration: underline;
}

/* Terms Section */
.terms-section {
  text-align: center;
  margin-top: 1.5rem;
}

.terms-text {
  font-size: 0.75rem;
  color: #9ca3af;
  line-height: 1.6;
  margin: 0;
}

.terms-link {
  color: #6ee7b7;
  text-decoration: none;
}

.terms-link:hover {
  text-decoration: underline;
}

/* ===========================
   FOOTER - Full Width
   =========================== */
.register-footer {
  background: #111827;
  padding: 1.25rem 2rem;
  text-align: center;
  border-top: 1px solid #374151;
  width: 100%;
  flex-shrink: 0;
}

.footer-text {
  font-size: 0.875rem;
  color: #9ca3af;
  margin: 0;
  font-weight: 400;
}

/* ===========================
   RESPONSIVE DESIGN
   =========================== */

/* Tablet and Below */
@media (max-width: 1024px) {
  .register-wrapper {
    flex-direction: column;
  }

  .left-panel,
  .right-panel {
    width: 100%;
    padding: 3rem 2rem;
  }

  .main-heading {
    font-size: 2.5rem;
  }

  .illustration-space {
    min-height: 150px;
    margin: 2rem 0;
  }
}

/* Mobile */
@media (max-width: 640px) {
  .left-panel,
  .right-panel {
    padding: 2rem 1.5rem;
  }

  .logo-section {
    margin-bottom: 2rem;
  }

  .brand-logo {
    height: 40px;
  }

  .main-heading {
    font-size: 2rem;
  }

  .subheading {
    font-size: 1rem;
  }

  .form-title {
    font-size: 1.75rem;
  }

  .features-section {
    gap: 1.5rem;
  }

  .feature-heading {
    font-size: 1.125rem;
  }
}

/* ===========================
   ANIMATIONS
   =========================== */

/* Form Elements Animation */
.register-form > * {
  animation: fadeInUp 0.5s ease-out;
  animation-fill-mode: both;
}

.register-form > *:nth-child(1) { animation-delay: 0.05s; }
.register-form > *:nth-child(2) { animation-delay: 0.1s; }
.register-form > *:nth-child(3) { animation-delay: 0.15s; }
.register-form > *:nth-child(4) { animation-delay: 0.2s; }
.register-form > *:nth-child(5) { animation-delay: 0.25s; }
.register-form > *:nth-child(6) { animation-delay: 0.3s; }
.register-form > *:nth-child(7) { animation-delay: 0.35s; }

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Dark Mode Support (Optional) */
@media (prefers-color-scheme: dark) {
  .register-container {
    background: transparent;
  }

  .right-panel {
    background: #1f2937;
  }
}
</style>
