from flask import *
from algorithm import *
app = Flask(__name__)

@app.route('/compute',methods=['POST'])
def compute():

    data = request.data
    dataDict = json.loads(data)
    print(dataDict['search_metadata']['query'])

    statuses = dataDict['statuses']
    spam_keywords = dataDict['spam_keywords']
    methodAlgorithm = dataDict['algoritma']

    hasilAlgoritma = check_is_spam(statuses, spam_keywords, methodAlgorithm)
    dataDict['statuses'] = hasilAlgoritma
    print(dataDict['search_metadata']['query'])

    hasilString = json.dumps(dataDict)
    return hasilString


if __name__ == '__main__':
    app.run(debug= True)