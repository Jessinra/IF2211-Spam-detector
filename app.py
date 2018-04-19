from flask import *
from test import *
app = Flask(__name__)

@app.route('/compute',methods=['POST'])
def compute():
    data = request.data
    dataDict = json.loads(data)
    spam_keywords = dataDict['spam_keywords']
    print(spam_keywords)



    return data


if __name__ == '__main__':
    app.run(debug= True)