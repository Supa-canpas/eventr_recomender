import requests
from bs4 import BeautifulSoup

class IventInfo:
    # self.infoUrl
    # self.info
    # self.ccurl 
    
    def __init__(self,url):
        self.ccurl = url

    def getUrl(self):
        # url = 'https://collabo-cafe.com/'
        res = requests.get(self.ccurl)
        soup = BeautifulSoup(res.text, 'html.parser')
        allUrldiv = soup.find('div','widget__post__list')
        allUrla = allUrldiv.find_all('a')
        self.infoUrl =[]
        for i in allUrla:
            buf = i["href"]
            self.infoUrl.append(buf)

    def getInfo(self,url):
        # url = 'https://collabo-cafe.com/events/collabo/shingeki-gigo-cafe-ikebukuro2024/'
        res = requests.get(url)
        soup = BeautifulSoup(res.text, 'html.parser')
        title = soup.find('h1').text
        news = soup.select_one('#main > article > section.entry-content > div.table__container > table')
        records = news.find_all('tr')
        self.info = []
        self.info.append(title)
        for i in range(1,3):
            if i == 3:
                buf = records[i].find('a')['href']
                self.info.append(buf)
            else:
                buf = records[i].find('td').text
                self.info.append(buf)

if __name__ == '__main__':
    # url = "https://collabo-cafe.com/events/collabo/shingeki-gigo-cafe-ikebukuro2024/"
    # info = getInfo(url)
    # print(info)

    iventinfo = IventInfo('https://collabo-cafe.com/')
    iventinfo.getUrl()
    for urli in iventinfo.infoUrl:  
        iventinfo.getInfo(urli)
        print(iventinfo.info)