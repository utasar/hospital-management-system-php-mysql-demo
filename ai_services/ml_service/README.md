# AI Dr. Care - Machine Learning Service

## Overview
This Python-based machine learning service provides advanced AI capabilities for symptom analysis and health risk prediction. It's designed to integrate seamlessly with the PHP backend via REST API.

## Features

### 1. Symptom Analysis
- Advanced pattern matching using Jaccard similarity
- Confidence scoring for diagnoses
- Disease severity classification
- Personalized recommendations based on age and gender

### 2. Health Risk Prediction
- Vital signs analysis
- BMI-based risk assessment
- Medical history consideration
- Overall risk scoring

## Installation

### Prerequisites
- Python 3.8 or higher
- pip (Python package manager)

### Setup
```bash
# Navigate to the ML service directory
cd ai_services/ml_service

# Install required packages
pip install -r requirements.txt
```

## Running the Service

### Development Mode
```bash
python app.py
```

The service will start on `http://localhost:5000`

### Production Mode
For production, use a WSGI server like Gunicorn:

```bash
pip install gunicorn
gunicorn -w 4 -b 0.0.0.0:5000 app:app
```

## API Endpoints

### 1. Analyze Symptoms
**Endpoint:** `POST /api/analyze_symptoms`

**Request Body:**
```json
{
  "symptoms": ["cough", "fever", "headache"],
  "patient_id": 123,
  "age": 35,
  "gender": "M"
}
```

**Response:**
```json
{
  "status": "success",
  "patient_id": 123,
  "symptoms": ["cough", "fever", "headache"],
  "diagnoses": [
    {
      "disease": "Influenza",
      "confidence": 75.5,
      "severity": "medium",
      "matched_symptoms": ["cough", "fever", "headache"],
      "all_symptoms": ["high fever", "cough", "body ache", "fatigue", "headache"]
    }
  ],
  "recommendation": {
    "action": "consultation",
    "message": "Symptoms suggest Influenza. Consider consulting a doctor.",
    "urgency": "medium",
    "specialization": "General Physician"
  },
  "timestamp": "2024-01-01T12:00:00"
}
```

### 2. Predict Health Risks
**Endpoint:** `POST /api/predict_risk`

**Request Body:**
```json
{
  "vital_signs": {
    "blood_pressure": "140/90",
    "heart_rate": 85,
    "temperature": 37.2,
    "bmi": 28.5
  },
  "age": 45,
  "medical_history": ["diabetes", "hypertension"]
}
```

**Response:**
```json
{
  "status": "success",
  "risks": [
    {
      "risk": "Hypertension",
      "level": "medium",
      "message": "Blood pressure is elevated. Monitor regularly."
    }
  ],
  "overall_risk_score": "medium",
  "timestamp": "2024-01-01T12:00:00"
}
```

### 3. Health Check
**Endpoint:** `GET /api/health`

**Response:**
```json
{
  "status": "healthy",
  "service": "AI Dr. Care ML Service",
  "version": "1.0.0",
  "timestamp": "2024-01-01T12:00:00"
}
```

## Integration with PHP Backend

### Enabling ML Service in PHP
Update `config/APIConfig.php`:

```php
const ML_API_ENDPOINT = 'http://localhost:5000/api/';
const ML_API_ENABLED = true;
```

### Example PHP Integration
```php
function analyzeWithMLService($symptoms, $patientId, $age, $gender) {
    $mlEndpoint = 'http://localhost:5000/api/analyze_symptoms';
    
    $data = [
        'symptoms' => $symptoms,
        'patient_id' => $patientId,
        'age' => $age,
        'gender' => $gender
    ];
    
    $ch = curl_init($mlEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
```

## Machine Learning Model

### Current Implementation
The service currently uses a **rule-based system** with:
- Disease-symptom knowledge base
- Jaccard similarity for pattern matching
- Weighted confidence scoring

### Future Enhancements
The architecture supports integration with:

1. **Scikit-learn Models**
   - Logistic Regression
   - Random Forest Classifier
   - Support Vector Machines

2. **Deep Learning Models**
   - TensorFlow/Keras neural networks
   - BERT for NLP-based symptom understanding
   - CNN for medical image analysis

3. **Pre-trained Medical AI**
   - IBM Watson Health
   - Google Health API
   - Medical BERT models

## Disease Database

Current database includes:
- Common Cold
- Influenza
- COVID-19
- Allergic Rhinitis
- Migraine
- Hypertension
- Type 2 Diabetes
- Asthma
- Gastroenteritis
- Anxiety Disorder

**Expanding the Database:**
Add new diseases in the `DISEASE_SYMPTOMS` dictionary in `app.py`:

```python
DISEASE_SYMPTOMS = {
    'New Disease': {
        'symptoms': ['symptom1', 'symptom2', 'symptom3'],
        'severity': 'low|medium|high'
    }
}
```

## Performance

### Scalability
- Horizontal scaling with multiple instances
- Load balancing recommended for production
- Caching for frequent queries

### Optimization
- Vectorized operations with NumPy
- Efficient similarity calculations
- Response caching for common symptom patterns

## Security

### Best Practices
- Use HTTPS in production
- Implement API authentication (JWT tokens)
- Rate limiting to prevent abuse
- Input validation and sanitization
- HIPAA compliance logging

### Example: Adding Authentication
```python
from functools import wraps

def require_api_key(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        api_key = request.headers.get('X-API-Key')
        if api_key != 'your-secret-key':
            return jsonify({'error': 'Invalid API key'}), 401
        return f(*args, **kwargs)
    return decorated_function

@app.route('/api/analyze_symptoms', methods=['POST'])
@require_api_key
def analyze_symptoms():
    # ... endpoint code
```

## Monitoring

### Logging
The service logs all requests and responses for debugging and monitoring.

### Health Monitoring
Use the `/api/health` endpoint for service monitoring and alerting.

## Testing

### Manual Testing
```bash
# Test symptom analysis
curl -X POST http://localhost:5000/api/analyze_symptoms \
  -H "Content-Type: application/json" \
  -d '{
    "symptoms": ["cough", "fever"],
    "patient_id": 1,
    "age": 30,
    "gender": "M"
  }'

# Test health check
curl http://localhost:5000/api/health
```

### Unit Tests
Create `test_app.py`:
```python
import unittest
from app import app

class TestMLService(unittest.TestCase):
    def setUp(self):
        self.app = app.test_client()
    
    def test_health(self):
        response = self.app.get('/api/health')
        self.assertEqual(response.status_code, 200)

if __name__ == '__main__':
    unittest.main()
```

## Deployment

### Docker Deployment
Create `Dockerfile`:
```dockerfile
FROM python:3.9-slim

WORKDIR /app
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY app.py .

EXPOSE 5000
CMD ["gunicorn", "-w", "4", "-b", "0.0.0.0:5000", "app:app"]
```

Build and run:
```bash
docker build -t ai-drcare-ml .
docker run -p 5000:5000 ai-drcare-ml
```

### Cloud Deployment
- **AWS:** Deploy on EC2 or Lambda with API Gateway
- **Google Cloud:** Use Cloud Run or App Engine
- **Azure:** Deploy on Azure Functions or App Service

## Contributing

To contribute to the ML service:
1. Add new diseases to the knowledge base
2. Improve similarity algorithms
3. Integrate pre-trained ML models
4. Add new health prediction features
5. Enhance documentation

## License

This ML service is part of the AI Dr. Care project.

## Support

For issues or questions about the ML service:
- Check the main README.md
- Review API documentation
- Test endpoints with provided examples

---

**AI Dr. Care ML Service** - Powering Intelligent Healthcare Decisions 🤖🏥
