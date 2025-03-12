<template>
  <div class="container">
    <h1>Create Deal and Account</h1>
    <form @submit.prevent="submitForm" class="form">
      <div class="form-group">
        <label for="deal_name">Deal Name</label>
        <input type="text" id="deal_name" v-model="dealName" required />
      </div>
      <div class="form-group">
        <label for="deal_stage">Deal Stage</label>
        <input type="text" id="deal_stage" v-model="dealStage" required />
      </div>
      <div class="form-group">
        <label for="account_name">Account Name</label>
        <input type="text" id="account_name" v-model="accountName" required />
      </div>
      <div class="form-group">
        <label for="account_website">Account Website</label>
        <input type="text" id="account_website" v-model="accountWebsite" required />
      </div>
      <div class="form-group">
        <label for="account_phone">Account Phone</label>
        <input type="text" id="account_phone" v-model="accountPhone" required />
      </div>
      <button type="submit" class="btn">Submit</button>
    </form>
    <div v-if="successMessage" class="success">{{ successMessage }}</div>
    <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      dealName: '',
      dealStage: '',
      accountName: '',
      accountWebsite: '',
      accountPhone: '',
      successMessage: '',
      errorMessage: ''
    };
  },
  methods: {
    async submitForm() {
  try {
    // Очистить сообщения перед отправкой формы
    this.successMessage = '';
    this.errorMessage = '';

    const response = await axios.post('/create-deal-and-account', {
      dealName: this.dealName,
      dealStage: this.dealStage,
      accountName: this.accountName,
      accountWebsite: this.accountWebsite,
      accountPhone: this.accountPhone
    });
    
    this.successMessage = 'Deal and Account created successfully';
  } catch (error) {
    console.log(error);
    if (error.response) {
      console.log(error.response);
      const responseData = error.response.data.error;
      
      if (responseData) {
        this.errorMessage = responseData;
        if (parsedData.data && parsedData.data[0].details.api_name === 'Website' && parsedData.data[0].details.expected_data_type === 'website') {
          // Логика для обработки ошибок, если нужно
        } else {
          const details = responseData.details.replace(/"/g, '');
          this.errorMessage = `${details}`;
        }
      } else {
        console.log(responseData.message);
        if (responseData.message === 'wrong phone number format') {
          this.errorMessage = 'Wrong phone number format';
        }
      }
    } else {
      this.errorMessage = `Failed to create deal and account: ${error.message}`;
    }

    // Очистить сообщение об успехе при ошибке
    this.successMessage = '';
  }
}
  }
};
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: 0 auto;
  margin-top: 10rem;
  padding: 20px;
  font-family: Arial, sans-serif;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
  font-weight: bold;
  text-align: center;
  color: #333;
}

.form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 15px;
}

label {
  margin-bottom: 5px;
  font-weight: bold;
  color: #555;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
}

.btn {
  padding: 10px 15px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
}

.btn:hover {
  background-color: #0056b3;
}

.success {
  margin-top: 20px;
  color: green;
  text-align: center;
}

.error {
  margin-top: 20px;
  color: red;
  text-align: center;
}
</style>