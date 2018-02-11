from flask import Flask,jsonify,json,request
from flask_restful import reqparse
import pymysql
import pandas as pd
import numpy as np
from statsmodels.tsa.arima_model import ARIMA

app = Flask(__name__)
@app.route('/predict', methods=["POST"])
def predict():
	try:
	
		# Parse the arguments
		parser = reqparse.RequestParser()
		parser.add_argument('idalat', type=str, help='idalatt')
		args = parser.parse_args()

		idalat = args['idalat']
		#print (idalat)
		
		#get datasensor from database save to dataframe
		conn = pymysql.connect(host='localhost', port=3306, user='root', passwd='', db='sigap')
		query = ("SELECT * FROM datasensor WHERE id_alat=" + idalat)
		#query = ("SELECT * FROM datasensor")
		df = pd.read_sql(query, conn, index_col='id')
		
		#take only our needed values
		df2 = df[["hpc", "created_at"]]
		series = df2.set_index("created_at")
		ts = series
		ts["hpc"] = ts["hpc"].astype("float")
		del ts["hpc"]
		#make random values for testing purpose only
		ts['rand'] = np.random.choice(range(1, 40), ts.shape[0])
		ts["rand"] = ts["rand"].astype("float")

		#make model
		model = ARIMA(ts, order=(2, 1, 2)) 
		results_AR = model.fit(disp=-1)

		#make prediction 5 values ahead
		pred = results_AR.predict((ts.shape[0]-1), (ts.shape[0]+9), dynamic=True)
		predValue = list(pred)
		
		# create a instances for filling up prediction list
		predList = []
		for i in range(0,len(predValue)):
			empDict = {"senVal": predValue[i]}
			predList.append(empDict)

		# convert to json data
		jsonStr = []
		jsonStr = predList
		return jsonify({"tasks" : jsonStr})
	
	except Exception as e:
		return {'error': str(e)}
	
	
	#return "hello world"+idalat
if __name__=="__main__":
    app.run(host= '0.0.0.0', port=33, debug=True)