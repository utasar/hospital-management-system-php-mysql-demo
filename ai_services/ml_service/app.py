#!/usr/bin/env python3
"""
AI Dr. Care - Machine Learning Service
Python-based AI service for advanced symptom analysis and health predictions

This service can be integrated with the PHP backend via REST API
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import numpy as np
import json
from datetime import datetime

app = Flask(__name__)
CORS(app)  # Enable CORS for PHP integration

# Disease-symptom matrix (simplified for demonstration)
# In production, this would be trained ML model
DISEASE_SYMPTOMS = {
    'Common Cold': {
        'symptoms': ['cough', 'runny nose', 'sore throat', 'sneezing', 'mild fever'],
        'severity': 'low'
    },
    'Influenza': {
        'symptoms': ['high fever', 'cough', 'body ache', 'fatigue', 'headache', 'chills'],
        'severity': 'medium'
    },
    'COVID-19': {
        'symptoms': ['fever', 'dry cough', 'loss of taste', 'loss of smell', 'fatigue', 'shortness of breath'],
        'severity': 'high'
    },
    'Allergic Rhinitis': {
        'symptoms': ['sneezing', 'runny nose', 'itchy eyes', 'congestion', 'watery eyes'],
        'severity': 'low'
    },
    'Migraine': {
        'symptoms': ['severe headache', 'nausea', 'sensitivity to light', 'visual disturbances', 'vomiting'],
        'severity': 'medium'
    },
    'Hypertension': {
        'symptoms': ['headache', 'dizziness', 'chest pain', 'shortness of breath', 'nosebleeds'],
        'severity': 'high'
    },
    'Type 2 Diabetes': {
        'symptoms': ['frequent urination', 'excessive thirst', 'fatigue', 'blurred vision', 'slow healing wounds'],
        'severity': 'high'
    },
    'Asthma': {
        'symptoms': ['wheezing', 'shortness of breath', 'chest tightness', 'coughing', 'difficulty breathing'],
        'severity': 'high'
    },
    'Gastroenteritis': {
        'symptoms': ['nausea', 'vomiting', 'diarrhea', 'abdominal pain', 'fever', 'dehydration'],
        'severity': 'medium'
    },
    'Anxiety Disorder': {
        'symptoms': ['rapid heartbeat', 'sweating', 'trembling', 'restlessness', 'difficulty concentrating'],
        'severity': 'medium'
    }
}


def calculate_similarity(patient_symptoms, disease_symptoms):
    """Calculate similarity between patient symptoms and disease symptoms"""
    patient_set = set(s.lower().strip() for s in patient_symptoms)
    disease_set = set(s.lower().strip() for s in disease_symptoms)
    
    # Calculate Jaccard similarity
    intersection = len(patient_set.intersection(disease_set))
    union = len(patient_set.union(disease_set))
    
    if union == 0:
        return 0
    
    jaccard_similarity = intersection / union
    
    # Calculate weighted score based on matching symptoms
    matched_symptoms = patient_set.intersection(disease_set)
    match_ratio = len(matched_symptoms) / len(disease_set) if len(disease_set) > 0 else 0
    
    # Combined score
    confidence = (jaccard_similarity * 0.4 + match_ratio * 0.6) * 100
    
    return round(confidence, 2)


@app.route('/api/analyze_symptoms', methods=['POST'])
def analyze_symptoms():
    """
    Analyze patient symptoms and return possible diagnoses
    
    Request body:
    {
        "symptoms": ["cough", "fever", "headache"],
        "patient_id": 123,
        "age": 35,
        "gender": "M"
    }
    """
    try:
        data = request.json
        symptoms = data.get('symptoms', [])
        patient_id = data.get('patient_id')
        age = data.get('age', 30)
        gender = data.get('gender', 'U')
        
        if not symptoms:
            return jsonify({
                'status': 'error',
                'message': 'No symptoms provided'
            }), 400
        
        # Analyze symptoms against disease database
        diagnoses = []
        
        for disease, info in DISEASE_SYMPTOMS.items():
            confidence = calculate_similarity(symptoms, info['symptoms'])
            
            if confidence > 20:  # Only include if confidence > 20%
                diagnoses.append({
                    'disease': disease,
                    'confidence': confidence,
                    'severity': info['severity'],
                    'matched_symptoms': list(set(s.lower() for s in symptoms) & 
                                           set(s.lower() for s in info['symptoms'])),
                    'all_symptoms': info['symptoms']
                })
        
        # Sort by confidence
        diagnoses.sort(key=lambda x: x['confidence'], reverse=True)
        
        # Generate recommendations
        recommendation = generate_recommendation(diagnoses, age, gender)
        
        return jsonify({
            'status': 'success',
            'patient_id': patient_id,
            'symptoms': symptoms,
            'diagnoses': diagnoses[:5],  # Top 5 diagnoses
            'recommendation': recommendation,
            'timestamp': datetime.now().isoformat()
        })
        
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500


def generate_recommendation(diagnoses, age, gender):
    """Generate health recommendations based on diagnoses"""
    if not diagnoses:
        return {
            'action': 'monitor',
            'message': 'No clear diagnosis found. Monitor symptoms and consult a doctor if they persist.',
            'urgency': 'low'
        }
    
    top_diagnosis = diagnoses[0]
    
    if top_diagnosis['severity'] == 'high':
        if top_diagnosis['confidence'] > 60:
            return {
                'action': 'urgent_consultation',
                'message': f'High probability of {top_diagnosis["disease"]}. Please consult a doctor immediately.',
                'urgency': 'high',
                'specialization': get_specialization(top_diagnosis['disease'])
            }
        else:
            return {
                'action': 'consultation',
                'message': f'Possible {top_diagnosis["disease"]}. Schedule a doctor appointment soon.',
                'urgency': 'medium',
                'specialization': get_specialization(top_diagnosis['disease'])
            }
    
    elif top_diagnosis['severity'] == 'medium':
        return {
            'action': 'consultation',
            'message': f'Symptoms suggest {top_diagnosis["disease"]}. Consider consulting a doctor.',
            'urgency': 'medium',
            'specialization': get_specialization(top_diagnosis['disease'])
        }
    
    else:
        return {
            'action': 'self_care',
            'message': f'Symptoms align with {top_diagnosis["disease"]}. Rest and monitor. Consult if worsens.',
            'urgency': 'low'
        }


def get_specialization(disease):
    """Map disease to medical specialization"""
    specialization_map = {
        'Common Cold': 'General Physician',
        'Influenza': 'General Physician',
        'COVID-19': 'Infectious Disease Specialist',
        'Allergic Rhinitis': 'Allergist',
        'Migraine': 'Neurologist',
        'Hypertension': 'Cardiologist',
        'Type 2 Diabetes': 'Endocrinologist',
        'Asthma': 'Pulmonologist',
        'Gastroenteritis': 'Gastroenterologist',
        'Anxiety Disorder': 'Psychiatrist'
    }
    
    return specialization_map.get(disease, 'General Physician')


@app.route('/api/predict_risk', methods=['POST'])
def predict_risk():
    """
    Predict health risks based on vital signs and history
    
    Request body:
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
    """
    try:
        data = request.json
        vital_signs = data.get('vital_signs', {})
        age = data.get('age', 30)
        medical_history = data.get('medical_history', [])
        
        risks = []
        
        # Analyze blood pressure
        bp = vital_signs.get('blood_pressure', '120/80')
        systolic, diastolic = map(int, bp.split('/'))
        
        if systolic >= 140 or diastolic >= 90:
            risks.append({
                'risk': 'Hypertension',
                'level': 'high' if systolic >= 160 else 'medium',
                'message': 'Blood pressure is elevated. Monitor regularly and consult cardiologist.'
            })
        
        # Analyze BMI
        bmi = vital_signs.get('bmi', 22)
        if bmi >= 30:
            risks.append({
                'risk': 'Obesity',
                'level': 'high',
                'message': 'BMI indicates obesity. Consider lifestyle modifications and nutrition counseling.'
            })
        elif bmi >= 25:
            risks.append({
                'risk': 'Overweight',
                'level': 'medium',
                'message': 'BMI indicates overweight. Regular exercise and balanced diet recommended.'
            })
        
        # Consider age factors
        if age > 50 and 'diabetes' in medical_history:
            risks.append({
                'risk': 'Cardiovascular Disease',
                'level': 'medium',
                'message': 'Age and diabetes history increase cardiovascular risk. Regular checkups recommended.'
            })
        
        return jsonify({
            'status': 'success',
            'risks': risks,
            'overall_risk_score': calculate_overall_risk(risks),
            'timestamp': datetime.now().isoformat()
        })
        
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500


def calculate_overall_risk(risks):
    """Calculate overall risk score"""
    if not risks:
        return 'low'
    
    high_count = sum(1 for r in risks if r['level'] == 'high')
    medium_count = sum(1 for r in risks if r['level'] == 'medium')
    
    if high_count >= 2:
        return 'high'
    elif high_count == 1 or medium_count >= 2:
        return 'medium'
    else:
        return 'low'


@app.route('/api/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'service': 'AI Dr. Care ML Service',
        'version': '1.0.0',
        'timestamp': datetime.now().isoformat()
    })


if __name__ == '__main__':
    print("🤖 AI Dr. Care ML Service Starting...")
    print("📊 Disease database loaded with", len(DISEASE_SYMPTOMS), "conditions")
    print("🌐 Service running on http://localhost:5000")
    print("\nEndpoints:")
    print("  - POST /api/analyze_symptoms")
    print("  - POST /api/predict_risk")
    print("  - GET  /api/health")
    
    app.run(host='0.0.0.0', port=5000, debug=True)
